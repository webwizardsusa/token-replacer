import * as TipTap from "@tiptap/core";

import Dropcursor from "@tiptap/extension-dropcursor";
import Gapcursor from "@tiptap/extension-gapcursor";
import HardBreak from "@tiptap/extension-hard-break";
import History from "@tiptap/extension-history";
import Paragraph from "@tiptap/extension-paragraph";
import Placeholder from "@tiptap/extension-placeholder";
import Document from "@tiptap/extension-document";
import Text from "@tiptap/extension-text";
import EditorInstance from "./lib/EditorInstance.js"
import Button from "./lib/Button.js"
import './lib/Registry.js'
import './Plugins/Bold.js'
import './Plugins/Italic.js'
import './Plugins/Underline.js'
import './Plugins/Strike.js'
import './Plugins/Link.js'
import PopperDialog from "./lib/PopperDialog.js";
import  "./Plugins/Blockquote.js";
import "./Plugins/Block.js";
import "./Plugins/ViewSource.js"
import './Plugins/Help.js';
import './Plugins/ListItem.js';
import './Plugins/OrderedList.js';
import './Plugins/UnorderedList.js';
import { isEqual } from "lodash";
import PasteEvent from "./Events/PasteEvent.js";
import ExternalsLoader from "./lib/ExternalsLoader.js";

let editorIds = 0;
let editorInstances = {};
let ServerCallId = 0;
let ServerCallCache = {};
/**
 * @typedef {typeof import("@tiptap/core")} TipTap
 */

/**
 * @type {{ FPRichEditor: { tiptap: TipTap } }}
 */
window.FPRichEditor.tiptap = TipTap;


/**
 * @type {{ FPRichEditor: { tiptap: TipTap } }}
 */

function editorInstanceFromStatePath(statePath) {
    for (let id in editorInstances) {
        let instance = editorInstances[id];
        if (statePath === instance.component.statePath) {
            return instance;
        }
    }
}


let LWBound = false;

let FPRichEditor = function({
    plugins,
    buttons,
    placeholder,
    statePath,
    stickyToolbar,
    state,
    withHelp,
    pluginExternals,
                            }) {
    return {
        editorId: null,
        focused: false,
        statePath,
        state,
        plugins,
        buttons,
        placeholder,
        stickyToolbar,
        withHelp,
        toolbar: null,
        pluginInstances: [],
        updatedAt: Date.now(),
        suppressUpdate: false,

        init() {

            if (!LWBound) {
                Livewire.on('modalAction', (event) => {
                    setTimeout(() => {
                        const proxyEvent = new CustomEvent('modal-action', { bubble: true, detail: event})
                        window.dispatchEvent(proxyEvent);
                    }, 100)
                })

                Livewire.on('fpServerResponse', (event) => {
                    let instance = editorInstanceFromStatePath(event.statePath);
                    if (instance && event.ident) {
                        let item = ServerCallCache[event.ident];
                        if (item) {
                            item.resolve(event.response);
                            delete ServerCallCache[event.ident];
                        }
                    }
                })
                LWBound=true;
            }
            this.loadExternals().then(() => {
                editorIds++;
                this.editorId = editorIds;
                this.createEditor()

                const updateSize = () => {
                    this.setToolbarTop();
                }

                this.$watch('state', (newState, oldState) => {
                    if (typeof newState !== "undefined") {
                        if (!isEqual(oldState, Alpine.raw(newState))) {

                            if (!this.suppressUpdate) {
                                this.setContent(newState)
                            }
                            this.suppressUpdate = false;
                        }
                    }
                });


                this.setToolbarTop = this.setToolbarTop.bind(this);

                // Cleanup event listener on component destroy
                this.$nextTick(() => {
                    window.addEventListener('resize', this.setToolbarTop);
                    this.setToolbarTop();

                })
            })

        },

        loadExternals() {
            return ExternalsLoader(pluginExternals);
        },

        destroy() {
                window.removeEventListener('resize', this.setToolbarTop);
                if (editorInstances[this.editorId]) {
                    delete editorInstances[this.editorId];
                }
        },
        setToolbarTop() {
            let toolbar = this.editorInstance().toolbar;
            if (!toolbar) {
                return;
            }

            if (!this.stickyToolbar) {
                if (toolbar.classList.contains('fp-rich-editor-toolbar-sticky')) {
                    toolbar.classList.remove('fp-rich-editor-toolbar-sticky');
                }
                return;
            }
            let el = document.querySelector('.fi-topbar')
            let top = el.classList.contains('sticky') ? el.offsetHeight : 0

            if (!toolbar.classList.contains('fp-rich-editor-toolbar-sticky')) {
                toolbar.classList.add('fp-rich-editor-toolbar-sticky');
            }
            toolbar.style.top = top + 'px'
        },

        _loadPlugin(plugin) {
            let tiptapExtensions = [];
            let config = {};
            if (typeof plugin === 'object') {
                config = plugin.config ? plugin.config : {};
                plugin = plugin.name;
            }
            let instance = window.FPRichEditor.plugins[plugin];
            if (!instance) {
                console.warn('Unknown editor plugin ' + plugin)
                return;
            }
            let pluginInstance = this.editorInstance().registerPlugin(plugin, instance, config)
            if(Array.isArray(pluginInstance.dependencies)) {
                pluginInstance.dependencies.forEach((dep) => {
                    if (!this.editorInstance().pluginRegistered(dep)) {
                        let deps = this._loadPlugin(dep);
                        tiptapExtensions = tiptapExtensions.concat(deps);
                    }
                })
            }
            if (pluginInstance.tiptap) {
                let tiptap = typeof pluginInstance.tiptap === 'function' ? pluginInstance.tiptap(config) : pluginInstance.tiptap;
                if (Array.isArray(tiptap)) {
                    tiptap.forEach((item) => {
                        tiptapExtensions.push(item)
                    })
                } else {
                    tiptapExtensions.push(tiptap)
                }
            }

            return tiptapExtensions;
        },
        getPlugins() {
            let defaults = [
                Document,
                Text,
                History,
                Dropcursor,
                Paragraph,
                Gapcursor,
                HardBreak,
            ]
            if (this.placeholder) {

                defaults.push(Placeholder.configure({
                    placeholder: this.placeholder
                }))
            }
            if (Array.isArray(this.plugins)) {
                this.plugins.forEach((plugin) => {
                    let extensions = this._loadPlugin(plugin);
                    defaults = defaults.concat(extensions);
                })
            }

            if (this.withHelp) {
                this.editorInstance().registerPlugin('help',window.FPRichEditor.plugins.help , {})

            }

            return defaults
        },

        /**
         *
         * @returns {import('./lib/EditorInstance.js').default}
         */
        editorInstance() {
          if (!editorInstances[this.editorId]) {
              editorInstances[this.editorId] = new EditorInstance(this);
          }

            return editorInstances[this.editorId]
        },

        buildToolbar() {
            let availableButtons = {};
            for (let key in this.editorInstance().plugins) {
                let plugin = this.editorInstance().plugins[key];
                if (plugin.buttons) {
                    plugin.buttons().forEach((button) => {
                        availableButtons[button.name] = new Button(button, plugin, this);
                    })
                }
            }

            toolbar = this.$el.querySelector('.fp-rich-editor-toolbar');
            this.editorInstance().setToolbar(toolbar);
            this.buttons.forEach((buttonName) => {
                if (buttonName === '|') {
                    let el = document.createElement('div');
                    el.classList.add('fp-rich-editor-separator');
                    toolbar.appendChild(el)
                    return;
                }
                let button = availableButtons[buttonName];
                if (button) {
                    toolbar.appendChild(button.render());
                    this.editorInstance().registerRenderedButton(buttonName, button)
                }
            })

            if (this.withHelp) {
                let button = availableButtons.help;
                let spacer = document.createElement('div');
                spacer.classList.add('fp-toolbar-spacer');
                toolbar.appendChild(spacer);
                toolbar.appendChild(button.render());
                this.editorInstance().registerRenderedButton('help', button)
            }
        },
        createEditor() {
            let el = this.$el.querySelector('.fp-rich-editor-content');
            let _this = this;
            let editorInstance = this.editorInstance(this);
            let editor = new TipTap.Editor({
                element: el,
                extensions: _this.getPlugins(),
                editable: !_this.disabled,
                content: this.state,
                onBlur(e) {
                    _this.focused = false;
                    _this.updatedAt = Date.now();

                },
                onFocus() {
                    _this.focused = true;
                    _this.updatedAt = Date.now();
                },
                onUpdate({editor}) {
                    _this.suppressUpdate = true;
                    _this.state = editor.isEmpty ? null : editor.getHTML();
                    _this.updatedAt = Date.now();
                    _this.selectionUpdated();
                },
                onSelectionUpdate() {
                    _this.selectionUpdated();
                },
                onPaste(event, slice) {
                    const clipboardData = event.clipboardData || window.clipboardData;
                    if (!clipboardData) {
                        console.log('Clipboard data not supported');
                        return;
                    }
                    // Check for image
                    let pasteEvent = new PasteEvent(editor, event, slice);
                    _this.callPluginHook('onPaste', event, slice, editor);

                },
            })
            editorInstance.bind(editor);
            this.buildToolbar();

        },

        requestFromServer(method, args) {
            return new Promise((resolve, reject) => {
                ServerCallId++;
                ServerCallCache[ServerCallId] = {
                    method,
                    args,
                    id: ServerCallId,
                    resolve,
                    reject,
                }

                this.$wire.dispatchFormEvent('fp-rich-editor::serverRequest', this.statePath, {
                    statePath: this.statePath,
                    ident: ServerCallId,
                    method,
                    args,
                });
            })
        },

        callPluginHook(hook, ...args) {
            let plugins = this.editorInstance().plugins;
            for(let plugin in plugins) {
                let instance = plugins[plugin];
                if (typeof instance[hook] === 'function') {
                    instance[hook](...args);
                }
            }
        },


        buttonAction(name) {
            let btn = this.editorInstance().getRegisteredButton(name);
            if (btn) {
                btn.exec(
                    this.editorInstance())
            }
        },

        selectionUpdated() {
            let toolbar = this.editorInstance().toolbar;
            toolbar.querySelectorAll('.fp-rich-editor-active-btn').forEach((btn) => {
                btn.classList.remove('fp-rich-editor-active-btn');
            })

            toolbar.querySelectorAll('[disabled]').forEach((btn) => {
                btn.removeAttribute('disabled');
            })
            let existingButtons = Object.values(this.editorInstance().renderedButtons);
            existingButtons.forEach((btn) => {
                if(btn.active(this.tiptap(), this)) {
                    btn.el.classList.add('fp-rich-editor-active-btn');
                }

                if (btn.disabled(this.tiptap(), this)) {
                    btn.el.setAttribute('disabled', 'disabled');
                }
            })
        },

        tiptap() {
          return this.editorInstance().editor;
        },

        createPopper(templateName, anchor) {
            let contents = this.$el.querySelector('template[name="' + templateName + '"]').innerHTML;
            return new PopperDialog(contents, anchor, this)
        },

        openModal(name, args = {}) {
            if (!args._coordinates) {
                args._coordinates = this.editorInstance().editor.view.state.selection.ranges
            }

            this.$wire.dispatchFormEvent('fp-rich-editor::showModal', this.statePath, {
                name,
                args,
            });

        },

        tiptapCommand(command, args = {}) {
            this.editorInstance().editor.chain().focus()[command](args).run();

        },

        modalAction(event) {
            let details = event.detail;
            if (details.statePath !== this.statePath) {
                return;
            }

            if (!details.action) {
                return;
            }
            let chain = this.editorInstance().editor.chain().focus();


            if (Array.isArray(details.coordinates) && details.coordinates.length) {
                chain.setTextSelection({from: details.coordinates[0].$from.pos, to: details.coordinates[0].$to.pos})
            }
            if (typeof this[details.action] === 'function') {
                this[details.action](details.args, chain);
            } else {

                chain[details.action](details.args);
                chain
                    .selectTextblockEnd()
                    .run();
            }
        },

        setContent(data) {
            let content = typeof data === 'string' ? data : data.content;
            let editor = this.tiptap();
            if (this.tiptap().isEditable) {
                const {from, to} = editor.state.selection;
                this.tiptap().commands.setContent(content);
                //editor.chain().focus().setTextSelection({from, to}).run();
            }

        },

        unsetLink(args = {}, chain = null) {
            chain = chain ? chain : this.editorInstance().editor.chain().focus();
            chain.extendMarkRange('link').unsetLink().selectTextblockEnd().run();
        },

        ajax(url, params = {}, method='post') {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            return new Promise((resolve, reject) => {
                fetch(url, {
                    method: 'POST', // HTTP method
                    headers: {
                        'Content-Type': 'application/json', // Inform the server you're sending JSON
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(params), // Convert the data object to a JSON string
                    credentials: 'include', // Include credentials such as cookies in the request
                })
                    .then((response) => {
                        if (!response.ok) {
                            reject(response);
                        } else {
                            response.json().then(data => {
                                resolve(data);
                            }).catch(error => {
                                reject(error);
                            })
                        }
                    })
                    .catch((error) => {
                        reject(error);
                    });
            })
        }
    }


}

function register() {
    Alpine.data('FPRichEditor', FPRichEditor)
}

if (window.Alpine) {
    register()
} else {
    document.addEventListener('alpine:init', () => {
       register()
    });
}

export default FPRichEditor;

