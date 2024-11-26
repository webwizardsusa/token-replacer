import {mdiHelp} from "@mdi/js";


window.FPRichEditor.registerPlugin('help', {

    label: 'Bold',
    active: 'bold',
    buttons() {
        return [
            {
                name: 'help',
                icon: mdiHelp,
                label: 'Help',
                action: function(editor, component) {
                    component.$wire.dispatchFormEvent('fp-rich-editor::showHelp', component.statePath, {});
                }
            }
        ]
    },
})
