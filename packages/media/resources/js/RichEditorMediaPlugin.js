import {mdiMultimedia} from "@mdi/js";
import {TextSelection} from 'prosemirror-state';

const urlRegex = /^(https?:\/\/)?([^\s.]+\.\S{2,})(:\d{2,5})?(\/\S*)?$/i;
let PreviewCache = {};

function getSelectedNode(editor) {
    const {selection} = editor.state;

    // Check if the selection is a node selection
    if (selection.node) {
        return selection.node;
    }

    // If it's a text selection, get the parent node of the selection
    const {$anchor} = selection;
    return $anchor.parent;
}

function isSelectedNodeAtEnd(editor) {
    const { selection, doc } = editor.state;

    // Get the selected node
    const selectedNode = getSelectedNode(editor);

    if (!selectedNode) {
        return false; // No node selected
    }

    // Get the position of the selection
    const { $anchor } = selection;

    // Calculate the end position of the selected node
    const nodeStart = $anchor.start(); // Start position of the selected node
    const nodeEnd = nodeStart + selectedNode.nodeSize;

    // Check if the node's end matches the document's size
    return nodeEnd === doc.content.size;
}

function openBrowser(editor, collection) {

    window.editor = editor;
    const { state, view } = editor;
    const { selection } = state;
    let node = getSelectedNode(editor);
    let data = {};
    if (node && node.type.name === 'media' && node.attrs.media) {
        data = node.attrs;
        if (data['data-preview']) {
            delete data['data-preview'];
        }
    }

    window.FPMediaBrowser.show()
        .inline()
        .selected(data?.media)
        .attributes(data)
        .collection(collection)
        .then((result) => {
            if (result?.length) {
                editor.commands.insertMedia(result[0]);
            }
            if (isSelectedNodeAtEnd(editor)) {
                const { $anchor } = selection;
                const nodeEndPos = $anchor.end();
                editor.chain().focus().insertContentAt(nodeEndPos, '<p></p>').run()
                const newParagraphPos = nodeEndPos + 1; // Start of the new paragraph
                editor.commands.setTextSelection(newParagraphPos);
            } else {
                let nextPos = editor.state.selection.$to.pos
                editor.commands.setTextSelection(nextPos+1);
                editor.chain().focus().run();
            }
            view.focus();
        })
}

function makeMediaNode(plugin) {
    return FPRichEditor.tiptap.Node.create({
        name: 'media',

        group: 'block', // This makes the node behave like a block-level element
        draggable: true,
        atom: false, // Atomic, cannot have child nodes

        addAttributes() {
            return {
                media: {
                    default: null, // URL for the oEmbed source
                },
                alt: {
                    default: '', // Title of the oEmbed
                },

                'link': {
                    default: '', // Provider name
                },
                'data-preview': {
                    default: '',
                },
                caption: {
                    default: null, // Default is null for no initial content
                    parseHTML: (element) => element.textContent, // Parse body from inner content
                },
                'align': {
                    default: '',
                }

            };
        },

        parseHTML() {
            return [
                {
                    tag: 'fp-media[media]',
                    getAttrs: (dom) => {
                        let preview = dom.getAttribute('data-preview');
                        let id = dom.getAttribute('media');
                        if (preview && id) {
                            PreviewCache[id] = preview;
                        }
                        return {
                            media: dom.getAttribute('media'),
                        }

                    },
                },
            ];
        },

        renderHTML({HTMLAttributes}) {
            let attributes = {...HTMLAttributes};
            if (Object.keys(attributes).includes('data-preview')) {
                delete attributes['data-preview'];
            }
            Object.keys(attributes).forEach(key => {
                if (!attributes[key]) {
                    delete attributes[key];
                }
            })
            // When outputting editor content as HTML, keep the original oembed tag
            return ['fp-media', attributes, attributes.caption || ''];
        },

        addNodeView() {
            return ({node, editor}) => {

                let dom = document.createElement('div');

                let preview = PreviewCache[node.attrs.media];
                if (preview) {
                    dom.innerHTML = preview;
                    dom = dom.firstElementChild;
                } else {
                    dom.innerText = 'Invalid media';
                }
                dom.classList.add('fp-media-node');
                dom.addEventListener('dblclick', (event) => {

                    openBrowser(plugin.component.editorInstance().editor, plugin.config.collection);
                });

                return {
                    dom,
                    contentDOM: null, // No editable content inside
                };
            };
        },

        addCommands() {
            return {

                insertMedia: attributes => ({commands, tr, editor}) => {
                    if (attributes['data-preview'] && attributes.media) {
                        PreviewCache[attributes.media] = attributes['data-preview'];
                        delete attributes['data-preview'];
                    }

                    const {from} = tr.selection; // Current selection position
                    const inserted = commands.insertContent({
                        type: this.name,
                        attrs: attributes,
                    });

                    // If the insertContent command was successful, move the cursor

                    return true; // Return true to indicate the command executed successfully
                },
            }
        },
    });
}

window.FPRichEditor.registerPlugin('media', {
    buttons() {
        return [
            {
                name: 'media',
                icon: mdiMultimedia,
                label: 'Insert/Create Media',
                active: 'media',
                action: function (editor) {
                    openBrowser(editor);
                }
            }
        ]
    },

    onPaste(event, slice, editor) {

    },
    tiptap: function () {
        return makeMediaNode(this);
    }
})

/**
 *
 * @param editor {import("@tiptap/core").Editor}
 * @param searchString
 * @returns {*[]}
 */


