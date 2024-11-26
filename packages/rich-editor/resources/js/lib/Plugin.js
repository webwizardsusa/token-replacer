
class Plugin {

    constructor(name, definition, config = {}, instance) {
        this.name = name;
        Object.entries(definition).forEach(([key, value]) => {
            if (typeof value === 'function') {
                // Bind methods to the instance
                this[key] = value.bind(this);
            } else {
                // Assign non-function properties directly
                this[key] = value;
            }
        });

        this.config = config;
        this._instance = instance;
    }

    get component() {
        return this._instance._component;
    }
}

export default Plugin;
