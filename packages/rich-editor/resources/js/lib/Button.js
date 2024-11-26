function renderIcon(icon) {
    let svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('viewBox', '0 0 24 24');
    svg.classList.add('ce-icon');
    svg.classList.add('fill-current')
    svg.classList.add('text-black')
    svg.innerHTML = '<path d="' + icon + '" />'
    return svg;
}


class Button {
    definition;
    _el

    constructor(definition, plugin, editor) {
        this.definition = definition;
        this._editor = editor;
        this._plugin = plugin;
    }

    get editor() {
        return this._editor;
    }

    get plugin() {
        return this._plugin;
    }

    isIOS() {
        return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    }

    get shortcut() {
        if (this.definition.shortcut) {
            let shortcut = this.definition.shortcut;
            if (this.isIOS()) {
                shortcut = shortcut.replace('Mod', 'cmd');
            } else {
                shortcut = shortcut.replace('Mod', 'ctrl');
            }
            return shortcut;
        }
    }
    render() {
        if (this.definition.render) {
            let el = this.definition.render.apply(this);
            if (el) {
                this._el = el;
            }
            return el;
        }
        let btn = document.createElement('button');
        btn.classList.add('fp-rich-editor-button');
        if (this.definition.icon) {
            btn.appendChild(renderIcon(this.definition.icon, btn))
        }

        btn.setAttribute('type', 'button')
        btn.setAttribute('x-on:click.prevent', 'buttonAction("' + this.definition.name + '")')
        let label = this.definition.label;
        let shortcut = this.shortcut;
        let labelStr = '';
        if (label) {
            labelStr = label;
            if (shortcut) {
                labelStr+=' ';
            }
        }

        if (shortcut) {
            labelStr+='(' + shortcut + ')';
        }
        if (labelStr) {
            btn.setAttribute('title', labelStr);
        }
        this._el = btn;
        return btn;
    }

    exec(editor, component) {
        this.definition.action.apply(this,[editor.editor, editor.component]);
    }

    active(editor) {
        let active = false;
        if (this.definition.active) {
            if (typeof this.definition.active === 'function') {
                return this.definition.active.apply(this);
            } else {
                return editor.isActive(this.definition.active);
            }

        }
    }

    disabled(editor) {
        let disabled = false;
        if (this.definition.disabled) {
            if (typeof this.definition.disabled === 'function') {
                return this.definition.disabled.apply(this, [editor]);
            } else {
                return !editor.can()[this.definition.disabled]();
            }
        }
    }

    get el() {
        return this._el;
    }
}


export default Button;
