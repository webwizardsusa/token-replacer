
import Plugin from './Plugin';
class EditorInstance {
    _editor;
    _plugins = {};
    _buttons = {};
    _renderedButtons = {};
    _toolbar

    constructor(component) {
        this._component = component;
    }

    get component() {
        return this._component;
    }
    setToolbar(el, component) {
        this._toolbar = el;
    }

    get toolbar() {
        return this._toolbar;
    }
    addButton(name, definition) {
        this._buttons[name] = definition;
        return this;
    }

    registerPlugin(name, instance, config = {}) {
        let plugin = new Plugin(name, instance, config, this);
        this._plugins[name] = plugin;
        return plugin;
    }

    pluginRegistered(name) {
        return this._plugins[name] !== undefined;
    }
    registerRenderedButton(name, button) {
        this._renderedButtons[name] = button;
    }

    getRegisteredButton(name) {
        return this._renderedButtons[name];
    }

    get renderedButtons() {
        return this._renderedButtons;
    }

    bind(editor) {
        this._editor = editor;
    }

    get editor() {
        return this._editor;
    }

    get buttons() {
        return this._buttons
    }

    get plugins() {
        return this._plugins;
    }
}

export default EditorInstance;
