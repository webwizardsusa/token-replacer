import {mdiYoutube} from "@mdi/js";
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
        name: 'oembed',

        group: 'block', // This makes the node behave like a block-level element
        draggable: true,
        atom: true, // Atomic, cannot have child nodes

        addAttributes() {
            return {
                src: {
                    default: null, // URL for the oEmbed source
                },
                title: {
                    default: '', // Title of the oEmbed
                },
                provider: {
                    default: '', // Provider name
                },
            };
        },

        parseHTML() {
            return [
                {
                    tag: 'oembed[src]',
                    getAttrs: (dom) => ({
                        src: dom.getAttribute('src'),
                        title: dom.getAttribute('title'),
                        provider: dom.getAttribute('provider'),
                    }),
                },
            ];
        },

        renderHTML({ HTMLAttributes }) {
            // When outputting editor content as HTML, keep the original oembed tag
            return ['oembed', HTMLAttributes];
        },

        addNodeView() {
            return ({ node, editor }) => {
                const dom = document.createElement('div');
                dom.classList.add('oembed-preview');

                const title = document.createElement('h5');
                title.textContent = (node.attrs.provider || 'Unknown provider')  + ' embed';
                dom.appendChild(title);

                const provider = document.createElement('span');
                provider.textContent = node.attrs.title ? node.attrs.title : node.attrs.src;
                dom.appendChild(provider);
                dom.addEventListener('dblclick', (event) => {
                    plugin.component.openModal('oembed-dialog', {src: node.attrs.src});
                });
                return {
                    dom,
                    contentDOM: null, // No editable content inside
                };
            };
        },

        addCommands() {
            return {
                insertOembed: attributes => ({ commands }) => {
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

window.FPRichEditor.registerPlugin('oembed', {
    buttons() {
        return [
            {
                name: 'oembed',
                icon: mdiYoutube,
                label: 'Embed content from another site',
                active: 'oembed',
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
        const pastedText = event.clipboardData.getData('text/plain').trim();
        let lines = pastedText.split('\n');
        let check = [];
        lines.forEach(line => {
            line = line.trim();
            if (check.indexOf(line) === -1) {
                try {
                    let test = new URL(line);
                    check.push(line);
                } catch(e) {

                }
            }
        })

        if (!check.length) {
            return;
        }

        this.component.requestFromServer('parseOembedLinks', {urls: check})
            .then((response) => {
                if (typeof response.results !== 'object' || Object.keys(response.results).length === 0) {
                    return;
                }

                let replacements = {};

                const { state, view } = editor;
                const tr = state.tr; // Create a new transaction


                for(let url in response.results) {
                    let oembed = response.results[url];
                    let results = findNodeByText(editor, url);
                    console.log(oembed);
                    if (results.length) {


                        results.forEach(({ node, pos }) => {
                            let oembedNode = editor.schema.nodes.oembed.create({
                                src: oembed.url,
                                provider: oembed.provider,
                                title: oembed.title,
                            });
                            // Calculate the range to replace the node
                            const from = pos;
                            const to = pos + node.nodeSize;

                            // Replace the node with the new content
                            tr.replaceWith(from, to, oembedNode);
                        });

                    }
                }

                if (tr.docChanged) {
                    view.dispatch(tr);
                }

                console.log(replacements, response);
            })

        /*
        https://www.youtube.com/watch?v=39j1ww9t62s

         */
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
function findNodeByText(editor, searchString) {
    const { state, view } = editor;
    let nodes = [];
    // Traverse the document
    state.doc.descendants((node, pos) => {
        if (node.type.name === 'paragraph') {
            const textContent = node.textContent.trim();
            if (textContent === searchString) {
                nodes.push({ node, pos });
            }
        }
    });

    return nodes;

}
