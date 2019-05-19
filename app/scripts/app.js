import http from './http';

import 'jquery';
import 'popper.js';
import 'bootstrap';

const form = document.getElementById('form');
const button = document.getElementById('button');
const tasks = document.getElementById("tasks");

form && form.addEventListener('submit', (event) => {
    event.preventDefault();

    const data = {};

    (new FormData(form)).forEach(function(value, key){
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

    (new FormData(login)).forEach(function(value, key){
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
