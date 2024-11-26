import link from "@tiptap/extension-link";
import {mdiLink, mdiLinkOff} from "@mdi/js";
import {createPopper} from '@popperjs/core';

window.FPRichEditor.registerPlugin('link', {

    buttons: function () {
        return [
            {
                name: 'unlink',
                icon: mdiLinkOff,
                label: 'Insert a link',
                action: function (editor, component) {
                    editor.chain().focus().unsetLink().run();
                }

            },
            {
                name: 'link',
                icon: mdiLink,
                active: 'link',
                disabled: 'setLink',
                action: function (editor, component) {
                    let link = editor.getAttributes('link');
                    component.openModal('link-dialog', link);

                }
            }
        ];
    },

    tiptap() {
        const {Mark, mergeAttributes} = FPRichEditor.tiptap
        return Mark.create({
            name: 'link',

            priority: 1000,

            keepOnSplit: false,
            exitable: true,
            addAttributes() {
                return {
                    href: {
                        default: null,
                        parseHTML(element) {
                            return element.getAttribute('href')
                        },
                    },
                    target: {
                        default: null,
                    },

                }
            },

            parseHTML() {
                return [
                    {
                        tag: 'a[href]',
                    },
                ]
            },

            renderHTML({ HTMLAttributes }) {
                return ['a', mergeAttributes(this.options.HTMLAttributes, HTMLAttributes), 0]
            },

            addCommands() {
                return {
                    setLink:
                        attributes => ({ chain }) => {
                            return chain().setMark(this.name, attributes).setMeta('preventAutolink', true).run()
                        },

                    toggleLink:
                        attributes => ({ chain }) => {
                            return chain()
                                .toggleMark(this.name, attributes, { extendEmptyMarkRange: true })
                                .setMeta('preventAutolink', true)
                                .run()
                        },

                    unsetLink:
                        () => ({ chain }) => {
                            return chain()
                                .unsetMark(this.name, { extendEmptyMarkRange: true })
                                .setMeta('preventAutolink', true)
                                .run()
                        },
                }
            },


        });
    }


})
