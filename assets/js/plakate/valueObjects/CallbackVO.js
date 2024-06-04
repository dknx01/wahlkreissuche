export class CallbackVO {
    constructor(callback, args) {
        this.callback = callback;
        this.arguments = args;
    }

    run() {
        return this.callback(...this.arguments);
    }
}