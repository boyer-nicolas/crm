import Api from "../core/Api";
import qs from "qs";

class DarkmodeHandler
{
    constructor()
    {
        const darkModeSwitch = document.querySelector("#darkmode-switch");
        const api = new Api();

        if (darkModeSwitch)
        {
            darkModeSwitch.onclick = () =>
            {
                console.log("click");
                darkModeSwitch.classList.toggle("active");
                document.body.classList.toggle("dark");

                let theme = '';
                if (document.body.classList.contains("dark"))
                {
                    theme = "dark";
                }
                else
                {
                    theme = "light";
                }

                api.post({
                    url: "/api/darkmode",
                    loadingMessage: null,
                    data: qs.stringify({
                        theme: theme,
                    }),
                    confirm: false,
                    quiet: true,
                    debug: false,
                }).catch((error) =>
                {
                    console.error("Darkmode not saved => " + error);
                });
            }
        }
    }
}

export default DarkmodeHandler;