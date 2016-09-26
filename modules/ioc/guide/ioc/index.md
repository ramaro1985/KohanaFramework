## IOC (*Inversion of Control*)

A hot topic in Java circles is *Inversion of Control Containers* also known 
as *Dependency Injection*.

There is a promising PHP5 project that’s a port of the original Java 
[Pico Container](http://www.picocontainer.org/).

This pattern is very useful because it inherently works well with the 
*Test Driven Development* methodology, allowing you to more readily test your code because 
it is designed to play nicely with other components right from the start.

This pattern is really orthogonal to MVC (and may be turn useless on HMVC enviroments), 
one of the areas I am most interested in is combining a Dependency Injection container like 
*Symfony Dependency Injection* component and *Kohana* MVC  framework to produce an application 
that *autowires* itself. Ideally this will create easy to assemble web applications, and at the 
same time allow for easily testable code by instructing the container to inject Mock Objects 
instead of real dependencies.

[!!] I choose *Symfony Dependency Injection* instead *Pico Container* because there is the only
one integration of Ioc and MVC Framework I found.

The rest of this book was writed by Fabien Potencier. The library source code was modified
to allow clean integration into *Kohana*. Send any idea or contribution to
[Rafael E. Espinosa Santiesteban](alvk4r@blackbird.org).
