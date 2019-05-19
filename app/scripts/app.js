import jQuery from 'jquery';
import 'popper.js';
import 'bootstrap';
import '@fortawesome/fontawesome-free';
import http from './http';

const form = document.getElementById('form');
const button = document.getElementById('button');
const tasks = document.getElementById("tasks");

form && form.addEventListener('submit', (event) => {
    event.preventDefault();

    const data = {};

    (new FormData(form)).forEach((value, key) => {
        data[key] = value;
    });

    http.post('/tasks/add.json', data).then((response) => {
        const data = response.data;

        if (data.result) {
            document.location = '/';

        } else {
            alert(data.error.message);
        }
    })
});

form && form.addEventListener('keyup', () => {
    if (form.checkValidity()) {
        button.removeAttribute('disabled');
    } else {
        button.setAttribute('disabled', 'disabled');
    }
});

const logout = document.getElementById("logout");

logout && logout.addEventListener('click', (event) => {
    event.preventDefault();

    http.post('/user/logout.json').then((response) => {
        const data = response.data;

        if (data.result) {
            document.location.reload();
        } else {
            alert(data.error.message);
        }
    })
});


const login = document.getElementById("login");

login && login.addEventListener('submit', (event) => {
    event.preventDefault();

    const data = {};

    (new FormData(login)).forEach((value, key) => {
        data[key] = value;
    });

    http.post('/user/login.json', data).then((response) => {
        const data = response.data;

        if (data.result) {
            document.location.reload();
        } else {
            alert(data.error.message);
        }
    })
});

tasks && tasks.querySelectorAll('.task-change-status').forEach(status => {
    status.addEventListener('change', event => {
        http.post('/tasks/changeStatus.json', {
            id: status.dataset.id,
            status: status.checked,
        }).then((response) => {
            const data = response.data;

            if (data.result) {
                let task = status.closest('.card');

                if (data.result.status) {
                    task.classList.add('border-success');
                } else {
                    task.classList.remove('border-success');
                }
            } else {
                alert(data.error.message);
            }
        });
    });
});

const popup = jQuery(document.getElementById('editModal'));
const editButton = popup.find('#edit-button');
const editors = tasks.querySelectorAll('.task-editor');

editors && editors.forEach(editor => {
    editor.addEventListener('click', event => {
        const id = editor.dataset.id;

        http.get(`/tasks/get.json?param[id]=${id}`).then((response) => {
            const data = response.data;

            if (data.result) {
                editButton.attr('disabled', 'disabled');
                popup.find('[name="text"]').val(data.result.text);
                popup.find(`[name="id"]`).val(data.result.id);
                popup.data('original', data.result.text);

                popup.modal('show');
            } else {
                alert(data.error.message);
            }
        })
    });
});

popup.find('[name="text"]').on('change keyup', (event) => {
    if (event.target.value !== popup.data('original')) {
        editButton.removeAttr('disabled');
    } else {
        editButton.attr('disabled', 'disabled');
    }
});


editButton.on('click', (event) => {
    const form = document.getElementById('edit-form');
    const data = {};

    (new FormData(form)).forEach((value, key) => {
        data[key] = value;
    });

    http.post('/tasks/changeText.json', data).then((response) => {
        const data = response.data;

        if (data.result) {
            document.location.reload();

        } else {
            alert(data.error.message);
        }
    });

    event.preventDefault();
});
