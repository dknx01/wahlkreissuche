import {CallbackVO} from "./CallbackVO";

export class LazyObject {
    constructor(callback, args) {
        this.callback = this.getCallback(callback, args);
        this.data = null;
    }

    getCallback(callback, args) {
        return new CallbackVO(callback, Array.isArray(args) ? args: [args]);
    }

    value() {
        if (this.data === null) {
            this.data = this.callback.run();
        }
        return this.data;
    }
}