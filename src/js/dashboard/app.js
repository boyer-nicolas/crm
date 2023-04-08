import Commons from "../core/Commons";
import PasswordInputSwitcher from "../core/PasswordInputSwitcher";
import ServiceWorker from "../core/ServiceWorker";
import PageHandler from "../handlers/PageHandler";
import SecurityHandler from "../handlers/SecurityHandler";
import "../lib/prism.js";
import "boxicons";
import "/node_modules/bootstrap/dist/js/bootstrap"

/**
 * Core Modules
 */
new Commons();
new PasswordInputSwitcher();
new ServiceWorker();
/**
 * Handlers
 */
new PageHandler();
new SecurityHandler();
