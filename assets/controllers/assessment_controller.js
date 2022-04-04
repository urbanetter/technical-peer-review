import { Controller } from '@hotwired/stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    static values = {
        url: String,
    }

    setValue(event) {
        console.log(event, this.urlValue);
        fetch(this.urlValue, {
            method: 'PUT',
            body: event.params.value
        })
            .then(response => response.text())
            .then(html => this.element.innerHTML = html)
        ;
    }
}
