
class MediaBrowserShow {

    _inline = false;
    _selected = false;
    _types = [];
    _options = {};
    _collection = null;
    then(callback) {
        this._callback = callback;
        return this;
    }

    types(types) {
        this._types = types;
        return this;
    }

    attributes(attributes) {
        this._attributes = attributes;
        return this;
    }
    selected(selected) {
        this._selected = selected;
        return this;
    }

    collection(collection) {
        this._collection = collection;
        return this;
    }
    inline(inline = true) {
        this._inline = inline;
        return this;
    }

    options(options) {
        this._options = options;
        return this;
    }
}
class MediaBrowserApi {
    constructor() {
        this._bound = false;
    }

    handleResults(results) {
        if (this._intent) {
            if (results.length) {
                this._intent._callback(results);
            } else {
                this._intent._callback(null);
            }
            this._intent = null;
        }
    }
    show() {
        if (!this._bound) {
            Livewire.on('filapress-media-browser-results', (e) => {
                this.handleResults(e);
            })
            this._bound = true;
        }
        this._intent = new MediaBrowserShow();
        setTimeout(() => {
            let options = {
                types: this._intent._types,
                selected: this._intent._selected,
                inline: this._intent._inline,
                attributes: this._intent._attributes,
                collection: this._intent._collection,
                ...this._intent._options
            }
            Livewire.dispatch('open-filapress-media-browser', {options});
        })
        return this._intent;
    }


}

window.FPMediaBrowser = new MediaBrowserApi();
setTimeout(() => {
    // window.FPMediaBrowser.showModal()

}, 250)


