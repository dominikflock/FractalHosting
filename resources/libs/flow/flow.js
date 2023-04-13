"use strict";

// Settings
var CSRF = null;
var FlowNodes = {};
var FlowDebounced = {};


// Request Package
class FlowRequestPackage {
    constructor() {
        this.formData = new FormData();
        this.data = new Object();
    }
    setRoute(route) {
        if (!route.startsWith(window.location.origin)) {
            if (route.startsWith("/")) {
                route = window.location.origin + route;
            }
            else {
                route = window.location.origin + "/" + route;
            }
        }
        if (route.endsWith("/")) {
            route = route.slice(0, -1);
        }
        this.route = route;
    }
    addData(key, value) {
        this.data[key] = value;
    }
    send(e) {
        if (CSRF === null) { console.log("No CSRF"); return; }
        if (FlowDebounced[this.route] !== undefined) { return; }
        FlowDebounced[this.route] = true;
        this.formData.append("data", JSON.stringify(this.data));
        let xhr = new XMLHttpRequest();
        xhr.open("POST", this.route);
        xhr.setRequestHeader("X-CSRF-TOKEN", CSRF);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                try {
                    let FlowResponseJSON = JSON.parse(xhr.responseText);
                    FlowActions.processResponseJSON(FlowResponseJSON);
                }
                catch (e) {
                    console.log("Something bad happened...");
                    console.log(e);
                }
                let route = xhr.responseURL.endsWith("/") ? xhr.responseURL.slice(0, -1) : xhr.responseURL;
                delete FlowDebounced[route];
                FlowActions.hideLoadersByEvent(e);
            }
        };
        xhr.send(this.formData);
    }
}

// Utilities
class FlowUtils {
    static generateUUID(length = 32) {
        var dt = new Date().getTime();
        var uuidPattern = 'x'.repeat(length).replace(/[xy]/g, function(c) {
            var r = (dt + Math.random()*16)%16 | 0;
            dt = Math.floor(dt/16);
            return (c=='x' ? r :(r&0x3|0x8)).toString(16);
        });
        return uuidPattern;
    }
    static findParentByClass(node, className) {
        if (node.classList !== undefined && node.classList.contains(className)) {
            return node;
        }
        else {
            let parent = node.parentNode;
            if (parent === null) { return null; }
            return FlowUtils.findParentByClass(node.parentNode, className);
        }
    }
}

// Handles
class FlowHandlers {
    static onClick(e) {
        if (e.target.nodeName == "BUTTON" || e.target.nodeName == "A") {
            FlowActions.showLoadersByEvent(e);
            let FlowRequest = FlowActions.makeRequestFromEvent(e);
            if (FlowRequest == null) { return; }
            e.preventDefault();
            FlowRequest.send(e);
        }
    }
    static onChange(e) {
        if (e.target.nodeName == "INPUT" || e.target.nodeName == "SELECT" || e.target.nodeName == "TEXTAREA") {
            FlowActions.showLoadersByEvent(e);
            let FlowRequest = FlowActions.makeRequestFromEvent(e);
            if (FlowRequest == null) { return; }
            FlowRequest.send(e);
        }
    }
}

// Actions
class FlowActions {
    static showLoadersByEvent(e) {
        let FlowLoaderID = e.target.getAttribute('flow-loader');
        let FlowLoaderNode = FlowLoaderID != null ? document.getElementById(FlowLoaderID) : null;
        if (FlowLoaderNode !== null) {
            FlowLoaderNode.style.display = "inline-block";
        }
    }
    static hideLoadersByEvent(e) {
        let FlowLoaderID = e.target.getAttribute('flow-loader');
        let FlowLoaderNode = FlowLoaderID != null ? document.getElementById(FlowLoaderID) : null;
        if (FlowLoaderNode !== null) {
            FlowLoaderNode.style.display = "none";
        }
    }
    static initDocument(doc) {
        doc.querySelectorAll('[flow]').forEach(element => {
            let FlowHashOrAttribute = element.getAttribute('flow');
            if (FlowHashOrAttribute == "" || FlowHashOrAttribute == null) { return; }
            if (FlowNodes[FlowHashOrAttribute] === undefined) {
                let FlowHash = FlowUtils.generateUUID();
                FlowNodes[FlowHash] = FlowHashOrAttribute;
                element.setAttribute('flow', FlowHash);
            }
        });
        return doc;
    }
    static getFlowKeyFromNode(node) {
        let FlowKey = node.getAttribute('flow');
        if (FlowKey == "" || FlowNodes[FlowKey] == undefined) { return null; }
        return FlowKey;
    }
    static makeRequestFromEvent(e) {
        let FlowRequest = new FlowRequestPackage();
        let FlowKey = FlowActions.getFlowKeyFromNode(e.target);
        if (FlowKey == null) { return null; }
        let ParentForm = e.target.closest("form");
        FlowRequest.setRoute(FlowNodes[FlowKey]);
        if (ParentForm == null) {
            return FlowRequest;
        }
        let Dataset = FlowActions.collectDatasetFromForm(ParentForm);
        Object.keys(Dataset).forEach(key => {
            FlowRequest.addData(key, Dataset[key]);
        });
        return FlowRequest;
    }
    static collectDatasetFromForm(form) {
        let Dataset = {};
        form.querySelectorAll('*[name]').forEach(element => {
            if (element.name != "" && element.value != undefined) {
                if (element.type == "checkbox" || element.type == "radio") {
                    if (element.checked == false) { return; }
                }
                Dataset[element.name] = element.value == "" || element.value == null ? null : element.value;
            }
        });
        return Dataset;
    }
    static processResponseJSON(FlowResponseJSON) {
        Object.keys(FlowResponseJSON).forEach(key => {
            switch (FlowResponseJSON[key]['action']) {
                case "reload":
                    window.location.reload();
                    break;
                case "redirect":
                    window.location.href = FlowResponseJSON[key]['url'];
                    break;
                case "popup":
                    FlowActions.popup(FlowResponseJSON[key]);
                    break;
                case "alert":
                    alert(FlowResponseJSON[key]['content']);
                    break;
                case "console":
                    if (FlowResponseJSON[key]['type'] == "log") {
                        console.log(FlowResponseJSON[key]['content']);
                        break;
                    }
                    else if (FlowResponseJSON[key]['type'] == "error") {
                        console.error(FlowResponseJSON[key]['content']);
                        break;
                    }
                    else if (FlowResponseJSON[key]['type'] == "warn") {
                        console.warn(FlowResponseJSON[key]['content']);
                        break;
                    }
                    break;
                case "modifyById":
                case "modifyByClass":
                case "modifyByQuery":
                case "modifyByQueryAll":
                    FlowActions.modifyDOM(FlowResponseJSON[key]);
                    break;
            }
        });
    }
    static popup(FlowResponseJSON) {
        let popup = document.createElement('div');
        popup.classList.add('popup');
        popup.innerHTML = FlowActions.initDOM(FlowResponseJSON.content);
        document.body.append(popup);
        setTimeout(function() {
            popup.classList.add('active');
        }, 100);
        popup.addEventListener("mouseenter", function() {
            popup.hold = true;
        });
        popup.addEventListener("mouseleave", function() {
            popup.hold = false;
        }, true);
        popup.addEventListener("click", FlowActions.attemptPopupDelete);
        if (FlowResponseJSON.duration != null) {
            setTimeout(function() {
                if (popup.hold == true) {
                    popup.addEventListener("mouseleave", FlowActions.attemptPopupDelete);
                }
                else {
                    FlowActions.attemptPopupDelete(popup);
                }
            }, FlowResponseJSON.duration);
        }
    }
    static attemptPopupDelete(popup) {
        if (popup instanceof MouseEvent) {
            if (popup.type == "click" && popup.target.classList.contains('popup-close')) {
                popup = FlowUtils.findParentByClass(popup.target, 'popup');
                if (popup == null) {
                    return;
                }
            }
            else if (popup.type == "mouseleave") {
                popup = FlowUtils.findParentByClass(popup.target, 'popup');
                if (popup == null) {
                    return;
                }
            }
            else {
                return;
            }
        }
        popup.classList.remove('active');
        popup.addEventListener("transitionend", function() {
            FlowActions.unflowDOM(popup);
            popup.remove();
        });
    }
    static initDOM(dom) {
        let div = document.createElement('div');
        div.innerHTML = dom;
        FlowActions.initDocument(div);
        return div.innerHTML;
    }
    // TODO - make this recursive to handle nested forms (?)
    static unflowDOM(dom) {
        dom.querySelectorAll('[flow]').forEach(element => {
            let FlowKey = element.getAttribute('flow');
            element.removeAttribute('flow');
            if (FlowNodes[FlowKey] !== undefined) {
                delete FlowNodes[FlowKey];
            }
        });
        if (dom.getAttribute('flow') !== null) {
            let FlowKey = dom.getAttribute('flow');
            dom.removeAttribute('flow');
            if (FlowNodes[FlowKey] !== undefined) {
                delete FlowNodes[FlowKey];
            }
        }
    }
    static modifyDOM(FlowResponseJSON) {
        let FlowTarget = null;
        switch (FlowResponseJSON['action']) {
            case "modifyById":
                FlowTarget = document.getElementById(FlowResponseJSON['selector']);
                break;
            case "modifyByClass":
                FlowTarget = document.getElementsByClassName(FlowResponseJSON['selector']);
                break;
            case "modifyByQuery":
                FlowTarget = document.querySelector(FlowResponseJSON['selector']);
                break;
            case "modifyByQueryAll":
                FlowTarget = document.querySelectorAll(FlowResponseJSON['selector']);
                break;
        }
        if (FlowTarget == null) { return; }
        if (FlowTarget instanceof NodeList ) {
            FlowTarget.forEach(node => {
                FlowActions.executeDOMModification(node, FlowResponseJSON);
            });
        } else if (FlowTarget instanceof HTMLCollection) {
            for (let i = FlowTarget.length-1; i >= 0; i--) {
                FlowActions.executeDOMModification(FlowTarget[i], FlowResponseJSON);
            }
        } 
        else {
            FlowActions.executeDOMModification(FlowTarget, FlowResponseJSON);
        }
    }
    static executeDOMModification(FlowTarget, FlowResponseJSON) {
        FlowActions.unflowDOM(FlowTarget);
        switch (FlowResponseJSON['method']) {
            case "outerhtml":
                FlowTarget.outerHTML = FlowActions.initDOM(FlowResponseJSON['content']);
                break;
            case "innerhtml":
                FlowTarget.innerHTML = FlowActions.initDOM(FlowResponseJSON['content']);
                break;
            case "text":
                FlowTarget.innerText = FlowResponseJSON['content'];
                break;
            case "attribute":
                FlowTarget.setAttribute(FlowResponseJSON['attribute'], FlowResponseJSON['content']);
                break;
            case "addClass":
                FlowTarget.classList.add(FlowResponseJSON['content']);
                break;
            case "removeClass":
                FlowTarget.classList.remove(FlowResponseJSON['content']);
                break;
            case "toggleClass":
                FlowTarget.classList.toggle(FlowResponseJSON['content']);
                break;
            case "innerappend":
                FlowTarget.innerHTML += FlowActions.initDOM(FlowResponseJSON['content']);
                break;
            case "innerprepend":
                FlowTarget.innerHTML = FlowActions.initDOM(FlowResponseJSON['content']) + FlowTarget.innerHTML;
                break;
            case "outerappend":
                FlowTarget.outerHTML += FlowActions.initDOM(FlowResponseJSON['content']);
                break;
            case "outerprepend":
                FlowTarget.outerHTML = FlowActions(FlowResponseJSON['content']) + FlowTarget.outerHTML;
                break;
            case "value":
                FlowTarget.value = FlowResponseJSON['content'];
                break;
            case "remove":
                FlowTarget.remove();
                break;
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    FlowActions.initDocument(document);
    document.addEventListener('click', FlowHandlers.onClick);
    document.addEventListener('change', FlowHandlers.onChange);
});