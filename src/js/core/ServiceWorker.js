class ServiceWorker
{
  constructor()
  {
    this.registerServiceWorker();
  }

  registerServiceWorker = async () =>
  {
    if ('serviceWorker' in navigator)
    {
      try
      {
        const registration = await navigator.serviceWorker.register(
          '/sw.js',
          {
            scope: '/',
          }
        );
      } catch (error)
      {
        console.error(`Registration failed with ${error}`);
      }
    }
  };

}

export default ServiceWorker;
