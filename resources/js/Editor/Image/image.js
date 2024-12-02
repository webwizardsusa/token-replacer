import {mdiImage} from "@mdi/js";
const urlRegex = /^(https?:\/\/)?([^\s.]+\.\S{2,})(:\d{2,5})?(\/\S*)?$/i;

function getSelectedNode(editor) {
    const { selection } = editor.state;

    // Check if the selection is a node selection
    if (selection.node) {
        return selection.node;
    }

    // If it's a text selection, get the parent node of the selection
    const { $anchor } = selection;
    return $anchor.parent;
}
function makeOembedNode(plugin) {
    return FPRichEditor.tiptap.Node.create({
        name: 'image',

        group: 'block', // This makes the node behave like a block-level element
        draggable: true,
        atom: true, // Atomic, cannot have child nodes

        addAttributes() {
            return {
                src: {
                    default: null, // URL for the oEmbed source
                },
                alt: {
                    default: '', // Title of the oEmbed
                },
                'image-id': {
                    default: '', // Provider name
                },
                'caption': {
                    default: '', // Provider name
                },
            };
        },

        parseHTML() {
            return [
                {
                    tag: 'img[src]',
                    getAttrs: (dom) => ({
                        src: dom.getAttribute('src'),
                        title: dom.getAttribute('title'),
                        provider: dom.getAttribute('provider'),
                        imageId: dom.getAttribute('image-id'),
                        caption: dom.getAttribute('caption'),
                    }),
                },
            ];
        },

        renderHTML({ HTMLAttributes }) {
            // When outputting editor content as HTML, keep the original oembed tag
            return ['img', HTMLAttributes];
        },

        addNodeView() {
            return ({ node, editor }) => {
                const dom = document.createElement('div');
                dom.classList.add('editor-image');


                return {
                    dom,
                    contentDOM: null, // No editable content inside
                };
            };
        },

        addCommands() {
            return {
                insertImage: attributes => ({ commands }) => {
                    return commands.insertContent({
                        type: this.name,
                        attrs: attributes,
                    });
                },
                removeOembed: attributes => ({ commands }) => {
                    if (!this.options.levels.includes(attributes.level)) {
                        return false
                    }

                    return commands.toggleNode(this.name, 'paragraph', attributes)
                },
            }
        },
    });
}

window.FPRichEditor.registerPlugin('image', {
    buttons() {
        return [
            {
                name: 'image',
                icon: mdiImage,
                label: 'Insert an image',
                active: 'image',
                action: function(editor, component) {
                    let node = getSelectedNode(editor);
                    let src = null;
                    if (node && node.type.name === 'oembed' && node.attrs.src) {
                        src = node.attrs.src
                    }
                    component.openModal('oembed-dialog', {src});
                }

            }
        ]
    },

    onPaste(event, slice, editor) {

    },
    tiptap: function() {
        return makeOembedNode(this);
    }
})

/**
 *
 * @param editor {import("@tiptap/core").Editor}
 * @param searchString
 * @returns {*[]}
 */

