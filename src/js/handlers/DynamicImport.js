import Settings from '../pages/Settings';
import Customers from '../pages/Customers';

class DynamicImport
{
    constructor()
    {
        new Settings();
        new Customers();
    }
}

export default DynamicImport;