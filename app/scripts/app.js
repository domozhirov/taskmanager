import http from './http';

const form = document.getElementById('form');
const button = document.getElementById('button');
const tasks = document.getElementById("tasks");

form.addEventListener('submit', (event) => {
    event.preventDefault();

    const data = {};

    (new FormData(form)).forEach(function(value, key){
        data[key] = value;
    });

    http.post('/tasks/add.json', data).then((response) => {
        const data = response.data;

        if (data.result) {
            tasks.insertAdjacentHTML('afterBegin', `
                <li class="list-group-item" data-toggle="modal" data-target="#editModal" data-task-id="${data.result.id}">
                    <strong>${data.result.name}</strong>  - <small>${data.result.email}</small>
                    <hr class="mt-1 mb-1">
                    ${data.result.text}
                    <i class="fa fa-window-close" aria-hidden="true"></i>
                </li>
            `);

            tasks.adjust

        } else {
            alert(data.error.message);
        }
    })
});

form.addEventListener('keyup', () => {
    if (form.checkValidity()) {
        button.removeAttribute('disabled');
    } else {
        button.setAttribute('disabled', 'disabled');
    }
});
