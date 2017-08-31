** KzAce **

Plugin pour [PluXml](http://pluxml.org), gestionaire de contenus ou de blog.

Ce plugin ajoute un éditeur de code avec coloration syntaxique.
Il utilise les librairies suivantes :

* [Ace](https://ace.c9.io/),
* [Emmet](https://docs.emmet.io/)
* [RequireJS](http://requirejs.org)

Le plugin comporte un patch à appliquer à Ace pour régler un problème avec la barre d'état et un grave problème avec Emmet.

En raison de conflits, plusieurs raccourcis ont été redéfinis:

* F11 pour passer un peli écran
* Ctrl-F11 pour accèder aux réglages de Ace
* Maj-F1 pour afficher la liste des racccourcis clavier

Selon la page HTML, l'éditeur est basculé dans le mode adéquat (xml, css, html, php, javascript).

Il est possible de redimensionner la fenêtre de l'éditeur. Toutefois pour actualiser la vue de l'éditeur, il faut cliquer à l'extérieur, puis à l'intérieur de la fenêtre.

Les styles d'affichage des pages de raccourcis clavier et de réglages de l'éditeur Ace ont été revus.

Sans rapport avec l'éditeur, une gestion d'onglets pour les deux feuilles de styles des plugins est ajoutée simplement avec quelques règles CSS et un bout de code javascript pour faciliter l'édition de ces feuilles.

Par défaut, les fenêtres d'éditeurs ont une hauteur minimum de 5 lignes et s'agrandissent au fur et à mesure du contenu dans la limite de 25 lignes.

Un script Bash permet d'installer les sources de Ace. Il est par contre nécessaire que Node soit installé.

Un autre script Bash permet de cosntruire l'archive Zip du plugin avec uniquement les éléments nécessaires.

Développé sous Ubuntu 16.10 et testé sous serveur Apache et NGinx.