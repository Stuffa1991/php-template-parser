# php-template-parser

PHP Template Parser that can replace strings in a html template

The structure is pretty simple
```
Views/index.html
```
Needs a 
```
Views/ViewData/index.json
```

Examples are already there

When trying to load index.php it per default try to load index.html template
If you want to load anything else give the index.php params index.php?page={desired_page}

And it will try and load that file from Views, its easy to put the params in .htaccess and load it naturraly with myPage.dk/page depending of you use nginx or apache
