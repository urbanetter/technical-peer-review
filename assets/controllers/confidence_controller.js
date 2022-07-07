import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        url: String,
    }

    setValue(event) {
        fetch(this.urlValue, {
            method: 'PUT',
            body: event.params.value
        })
            .then(response => response.text())
            .then(html => this.element.innerHTML = html)
        ;
    }
}
