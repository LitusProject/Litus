Litus [![Build Status](https://travis-ci.org/LitusProject/Litus.svg?branch=master)](https://travis-ci.org/LitusProject/Litus) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/LitusProject/Litus/badges/quality-score.png?s=0d13a9ff23150e59e7b76e017699c17fd92fa91a)](https://scrutinizer-ci.com/g/LitusProject/Litus/)
=====

<p align="center">
    <img src="https://github.com/LitusProject/Litus/raw/master/public/img/litus.png" height="250px" />
</p>

## Welcome
You are running a student organization and need some supporting applications, but don't have the time or experience to build your own platform? This is an overview of what is currently implemented in our system.

* General membership administration.
* An extensive book store platform which allows professors to upload notes that should be printed and makes it easy for students to place a reservation, avoiding endless queues.
* A company platform, where companies can be promoted to the students. Furthermore, there is also the possibility for last year students to enter their Curriculum Vitae, which are then published to recruiting teams.
* Run a front-end website, where news items or notifications can be posted, events can be announced and where information about the organisation can be published. All this can be done from an easy web interface while writing MarkDown instead of pesky HTML. The entire front-end is multilingual, making it even accessible to exchange students.

This is only the tip of the iceberg and there is a lot more inside! You can easily try it out by installing it locally. You'll find some installation instructions on our [wiki](https://github.com/LitusProject/Litus/wiki).

## License
Because we spent a great deal of time on this project, we thought it would be nice to release all our code under the AGPLv3 license. That way, you can contribute to it if you have a great idea or you think something's missing. We also wrote a lot of code to use various protocols and to integrate with other applications.

The following files are exempt from the AGPLv3 license:
- `config/{application,database,lilo}.config.php`, due to being configuration rather than code. These files are released under the MIT license.
- `public/img/glyphicons-halflings{,-white}.png` are part of [Bootstrap](http://getbootstrap.com/) and [thus](http://glyphicons.com/license/) licensed under the [MIT license](https://github.com/twbs/bootstrap/blob/master/LICENSE).
- `public/_gollum/img/icon-sprite.png` is part of [Gollum](https://github.com/gollum/gollum), licensed as [CC-BY-SA](https://github.com/gollum/gollum/blob/master/licenses/licenses.txt) v3 or later.
- The images in `public/{img/jquery_ui,_logistics/theme/images}`are part of [jQuery UI](http://jqueryui.com/) and [licensed](https://jquery.org/license/) under the MIT License.
- All images containing a reference to [VTK](http://vtk.be) are &copy; Vlaamse Technische Kring vzw.
- All images containing a reference to [Litus](http://litus.cc) or [Student IT](http://studentit.be) are &copy; Student IT vzw.
- All other images are licensed [CC-BY-ND v4 or later](https://creativecommons.org/licenses/by-nd/4.0/) unless otherwise denoted in a readme or license file in a parent directory of the file.
- All JavaScript and CSS files containing an explicit license header.

## Contributing
Wrote a nice new feature? Solved a bug? Just shoot us a pull request and we'll review it! However, when contributing some code to the project, please take a look at our [Code Style](https://github.com/LitusProject/Litus/wiki/Style) first.  
We expect code to have passed through [`php-cs-fixer`](https://github.com/fabpot/PHP-CS-Fixer) before being committed. You can easily achieve this by using a pre-commit git hook as described [here](https://github.com/LitusProject/PhpCodeStyle/tree/master/Resources/git-hooks).

## Authors
Built by [@nielsavonds](https://github.com/nielsavonds), [@koencertyn](https://github.com/koencertyn), [@bgotink](https://github.com/bgotink), [@DarioI](https://github.com/DarioI), [@Pmaene](https://github.com/Pmaene), [@krmarien](https://github.com/krmarien), [@vierbergenlars](https://github.com/vierbergenlars) and [@dwendelen](https://github.com/dwendelen) with all the love in the world. If you have any questions, don't hesitate to contact us.
