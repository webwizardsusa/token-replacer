import { createPopper } from '@popperjs/core';
let instanceCount = 0;
class PopperDialog {
    _placement='bottom';
    _closeOnClickout=true;
    _closeOnEscape=true;
    _script = null;
    _popper
    _el
    _title
    _dataMethod

    constructor(contents, anchor, editor) {
        this._contents = contents;
        this._anchor = anchor;
        this.checkClickout = this.checkClickout.bind(this);
        this.checkForEscape = this.checkForEscape.bind(this);
        this._editor = editor;
    }

    closeOnClickout(bool) {
        this._closeOnClickout = bool;
        return this;
    }
    closeOnEscape(bool) {
        this._closeOnEscape = bool;
        return this;
    }

    title(title) {
        this._title = title;
        return this;
    }

    script(script) {
        this._script = script;
        return this;
    }
    show() {
        this._el = document.createElement('div');
        this._el.classList.add('fp-rich-editor-popper')
        let arrow = document.createElement('div');
        arrow.classList.add('fp-rich-editor-popper-arrow');
        arrow.innerHTML = '<div class="arrow-inner"></div>'
        this._el.appendChild(arrow);
        let inner = document.createElement('div');
        inner.classList.add('fp-rich-editor-popper-inner')
        if (this._title) {
            let titleEl = document.createElement('div');
            titleEl.classList.add('fp-rich-editor-popper-title');
            titleEl.innerText = this._title;
            inner.appendChild(titleEl);
        }

        let body = document.createElement('div');
        body.classList.add('fp-rich-editor-popper-body');

        if (this._script) {
            instanceCount++;
            this._dataMethod = 'TipTapDialogData' + instanceCount;
            body.setAttribute('x-data', this._dataMethod)
            window[this._dataMethod] = this._script;
            window[this._dataMethod].editor = this._editor;
            let that = this;
            window[this._dataMethod].closePopper = function() {
                that.destroy();
            }

        }

        body.innerHTML = this._contents;
        inner.appendChild(body);
        this._el.appendChild(inner);
        document.querySelector('body').append(this._el);

        this._popper = createPopper(this._anchor, this._el, {
            placement: this._placement,
            modifiers: [
                {
                    name: 'arrow',
                    options: {
                        element: arrow, // Specify the arrow element
                        padding: 5, // Optional: Add padding between arrow and Popper edges
                    },
                },
                {
                    name: 'offset',
                    options: {
                        offset: [0, 10], // Space between Popper and reference
                    },
                },
            ],
        });

        if (this._closeOnClickout) {
            document.addEventListener('mousedown', this.checkClickout);
        }

        if (this._closeOnEscape) {
            document.addEventListener('keyup', this.checkForEscape)
        }

        setTimeout(() => {
            this._el.querySelector('[autofocus]').focus();
        })

        return this;
    }

    checkForEscape(e) {
        if (e.key === 'Escape') {
            this.destroy();
        }
    }

    checkClickout(e) {
        if (!this._el.contains(e.target)) {
            this.destroy()
        }
    }

    destroy() {
        document.removeEventListener('mousedown', this.checkClickout);
        document.removeEventListener('keyup', this.checkForEscape)
        if (this._dataMethod) {
            delete window[this._dataMethod]
        }
        if (this._popper) {
            this._popper.destroy();
            this._popper = null;
            this._el.remove();
            this._el = null;
        }

    }

}
export default PopperDialog
