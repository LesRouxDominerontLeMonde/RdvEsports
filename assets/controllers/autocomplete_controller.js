import { Controller } from 'stimulus';

export default class extends Controller {
    static values = {
        url: String
    }

    connect() {
        this.element.addEventListener('input', this.onInput.bind(this));
    }

    onInput(event) {
        const query = event.target.value;
        if (query.length < 2) {
            return;
        }

        fetch(`${this.urlValue}?ville=${query}`)
            .then(response => response.json())
            .then(data => {
                // Handle the autocomplete suggestions here
                console.log(data);
                // You can update the UI with the suggestions
            });
    }
}
