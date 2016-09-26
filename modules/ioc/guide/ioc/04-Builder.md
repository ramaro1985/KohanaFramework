# Using a Builder to create Services

In the previous chapter, you learned how to use the [Service_Container] class
to provide a more appealing interface to your service containers. In this
chapter, we will go one step further and learn how to leverage the
[Service_Container_Builder] class to describe services and their configuration
in pure PHP code.

The [Service_Container_Builder] class extends the basic [Service_Container]
class and allows the developer to describe services with a simple PHP
interface.

[!!]**The Service Container Interface**:  
All service container classes share the same interface, defined in [Service_Container_Interface].

The descriptions of the services are done by registering service definitions.
Each service definition describes a service: from the class to use to the
arguments to pass to the constructor, and a bunch of other configuration
properties (see the [Service_Definition] note below).

The `Zend_Mail` example can easily be rewritten by removing all the hardcoded
code and building it dynamically with the builder class instead:

    [php]
    require_once '/PATH/TO/service/container/autoloader.php';
    Service_Container_Autoloader::register();

    $sc = new Service_Container_Builder();

    $sc ->register('mail.transport', 'Zend_Mail_Transport_Smtp')
        ->addArgument('smtp.gmail.com')
        ->addArgument(array(
            'auth'     => 'login',
            'username' => '%mailer.username%',
            'password' => '%mailer.password%',
            'ssl'      => 'ssl',
            'port'     => 465,))
        ->setShared(false);

    $sc ->register('mailer', '%mailer.class%')
        ->addMethodCall('setDefaultTransport', 
            array(new Service_Reference('mail.transport'))
        );

The creation of a service is done by calling the `register()` method, which
takes the service name and the class name, and returns a [Service_Definition]
instance.

[!!]A service definition is internally represented by an object of
class [Service_Definition]. It is also possible to create one by
hand and register it directly by using the service container
`setServiceDefinition()` method.

The definition object implements a fluid interface and provides
methods that configure the service. In the above example, we have used the
following:

  * `addArgument()`: Adds an argument to pass to the service constructor.

  * `setShared()`: Whether the service must be unique for a container or not
    (`true` by default).

  * `addMethodCall()`: A method to call after the service has been created.
    The second argument is an array of arguments to pass to the method.

Referencing a service is now done with a [Service_Reference] instance. This
special object is dynamically replaced with the actual service when the
referencing service is created.

During the registration phase, no service is actually created, it is just
about the description of the services. The services are only created when you
actually want to work with them. It means you can register the services in any
order without taking care of the dependencies between them. It also means you
can override an existing service definition by re-registering a service with
the same name. That's yet another simple way to override a service for testing
purposes.

**The [Service_Definition] Class**:

A service has several properties that change the way it is created and configured:

* [Service_Definition::setConstructor()]: Sets the static method to use when the service is 
created, instead of the standard `new` construct (useful for factories).
* [Service_Definition::setClass()]: Sets the service class.
* [Service_Definition::setArguments()]: Sets the arguments to pass to the constructor 
(the order is of course significant).
* [Service_Definition::addArgument()]: Adds an argument for the constructor.
* [Service_Definition::setMethodCalls()]: Sets the service methods to call after service creation.
These methods are called in the same order as the registration.
* [Service_Definition::addMethodCall()]: Adds a service method call to call after service creation. 
You can add a call to the same method several times if needed.  
* [Service_Definition::setFile()]: Sets a file to include before creating a service (useful if the 
service class if not autoloaded).
* [Service_Definition::setShared()]: Whether the service must be unique for a container or not 
(`true` by default).
* [Service_Definition::setConfigurator()]: Sets a PHP callable to call after the service has 
been configured.

As the [Service_Container_Builder] class implements the standard
[Service_Container_Interface] interface, using the service container does not
need to be changed:

    [php]
    $sc->addParameters(array(
        'mailer.username' => 'foo',
        'mailer.password' => 'bar',
        'mailer.class'    => 'Zend_Mail',
    ));

    $mailer = $sc->mailer;

The [Service_Container_Builder] is able to describe any object instantiation
and configuration. We have demonstrated it with the `Zend_Mail` class, and
here is another example using the `sfUser` class from Symfony:

    [php]
    $sc = new Service_Container_Builder(array(
        'storage.class'        => 'sfMySQLSessionStorage',
        'storage.options'      => array('database' => 'session', 'db_table' => 'session'),
        'user.class'           => 'sfUser',
        'user.default_culture' => 'en',
    ));

    $sc->register('dispatcher', 'sfEventDispatcher');

    $sc ->register('storage', '%storage.class%')
        ->addArgument('%storage.options%');

    $sc ->register('user', '%user.class%')
        ->addArgument(new Service_Reference('dispatcher'))
        ->addArgument(new Service_Reference('storage'))
        ->addArgument(array('default_culture' => '%user.default_culture%'));

    $user = $sc->user;

[!!]In the Symfony example, even though the storage object takes an
array of options as an argument, we passed a string placeholder
(`addArgument('%storage.options%')`). The container is smart enough
to actually pass an array, which is the value of the placeholder.

Using PHP code to describe the services is quite simple and powerful. It gives
you a tool to create your container without duplicating too much code and to
abstract object instantiation and configuration.
