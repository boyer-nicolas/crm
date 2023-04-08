import Pristine from '../lib/pristinejs/src/pristine';
import Api from "../core/Api";

class SecurityHandler
{
    constructor()
    {
        this.loginForm = document.querySelector("#login-form");
        if (this.loginForm)
        {
            this.handleLoginForm();
        }

        this.logoutBtn = document.querySelector("#logout-btn");
        if (this.logoutBtn)
        {
            this.handleLogoutBtn();
        }
    }

    handleLoginForm()
    {
        let config = {
            // class of the parent element where the error/success class is added
            classTo: 'form-group',
            errorClass: 'has-danger',
            successClass: 'has-success',
            // class of the parent element where error text element is appended
            errorTextParent: 'form-group',
            // type of element to create for the error text
            errorTextTag: 'div',
            // class of the error text element
            errorTextClass: 'text-help'
        };

        let validator = new Pristine(this.loginForm, config);

        const form = this.loginForm;

        form.addEventListener("submit", function (event)
        {
            event.preventDefault();
            let valid = validator.validate();
            const api = new Api();
            const data = new FormData(form);

            if (valid)
            {
                api.post({
                    url: "/api/login",
                    loadingMessage: "Connexion cours...",
                    data: data,
                    confirm: false,
                    quiet: false,
                    debug: true,
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(function (response)
                {
                    if (response.type === "error")
                    {
                        form.querySelector('input[name="password"]').value = "";
                    }
                });
            }
        });
    }

    handleLogoutBtn()
    {
        this.logoutBtn.addEventListener("click", function (e)
        {
            const api = new Api();
            e.preventDefault();
            api.post({
                url: "/api/logout",
                loadingMessage: "Déconnexion...",
                data: null,
                confirm: false,
                quiet: false,
                debug: false
            });
        });
    }
}

export default SecurityHandler;