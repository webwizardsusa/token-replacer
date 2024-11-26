

function makeHeadingNode(levels) {
    return FPRichEditor.tiptap.Node.create({
        name: 'heading',

        addOptions() {
            return {
                levels: levels,
                HTMLAttributes: {},
            }
        },

        content: 'inline*',

        group: 'block',

        defining: true,

        addAttributes() {
            return {
                level: {
                    default: 1,
                    rendered: false,
                },
            }
        },

        parseHTML() {
            return this.options.levels
                .map((level) => ({
                    tag: `h${level}`,
                    attrs: { level },
                }))
        },

        renderHTML({ node, HTMLAttributes }) {
            const hasLevel = this.options.levels.includes(node.attrs.level)
            const level = hasLevel
                ? node.attrs.level
                : this.options.levels[0]

            return [`h${level}`, FPRichEditor.tiptap.mergeAttributes(this.options.HTMLAttributes, HTMLAttributes), 0]
        },

        addCommands() {
            return {
                setHeading: attributes => ({ commands }) => {
                    if (!this.options.levels.includes(attributes.level)) {
                        return false
                    }

                    return commands.setNode(this.name, attributes)
                },
                toggleHeading: attributes => ({ commands }) => {
                    if (!this.options.levels.includes(attributes.level)) {
                        return false
                    }

                    return commands.toggleNode(this.name, 'paragraph', attributes)
                },
            }
        },
    });
}

window.FPRichEditor.registerPlugin('blocks', {

    label: 'Blocks',
    active: 'bold',
    buttons() {
        return [
            {
                name: 'blocks',
                render() {
                    let select = document.createElement('select');
                    select.classList.add('fp-rich-editor-select-button');
                    let paragraph = document.createElement('option');
                    paragraph.value = 'p';
                    paragraph.textContent = 'Paragraph';
                    select.appendChild(paragraph);
                    this.commandMap = {
                        p: {
                            command: 'setParagraph',
                            attrs: {},
                        }
                    };
                    if (Array.isArray(this.plugin.config?.blocks)) {
                        this.plugin.config.blocks.forEach(item => {
                            let option = document.createElement('option');
                           this.commandMap[item.ident] = item;
                            option.value = item.ident;
                            option.textContent = item.label;
                            select.appendChild(option);
                        })

                    }

                    select.onchange = (e) => {
                        let command = this.commandMap[e.target.value];
                        if (command) {
                            this.editor.tiptapCommand(command.command, command.attrs);
                        }
                    }
                    this.select = select;
                    return select;
                },

                active(editor) {
                    let active = 'p';

                    for (let key in this.commandMap) {
                        let command = this.commandMap[key];
                        if (command.active) {
                            if (this.editor.tiptap().isActive(command.active, command.attrs)) {
                                active=key;
                            }
                        }
                    }
                    this.select.value = active;
                },

                disabled(editor) {
                    let enabled = false;
                    let disabled = [];
                    for (let key in this.commandMap) {
                        let command = this.commandMap[key];
                        if (command.disabled) {

                        } else {
                            let result = editor.can()[command.command](command.attrs)
                            if (!result) {
                                disabled.push(command.command);
                            }
                        }
                    }
                   return disabled.length === Object.keys(this.commandMap).length;
                }

            }
        ]
    },

    headingLevels: [],



    tiptap: function() {
        let extensions = [];
        this.headingLevels = [];
        if (Array.isArray(this.config?.blocks)) {
            this.config.blocks.forEach(item => {
                if (item.attrs?.level) {
                    this.headingLevels.push(item.attrs.level);
                }
            })
        }
        if (this.headingLevels.length > 0) {
            extensions.push(makeHeadingNode(this.headingLevels));
        }
        return extensions;

    }
})
