

class PasteEvent {
    constructor(editor, event, slice) {
        this.editor = editor;
        this.event = event;
        this.slice = slice;

        const { state, commands } = editor;
        const { selection, doc } = state;
        this.selection = selection;
    }

    get isNewLine() {
        return this.selection.$from.parent.content.size === 0
    }
}

export default PasteEvent;
