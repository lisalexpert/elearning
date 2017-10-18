<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Automatically generated strings for Moodle installer
 *
 * Do not edit this file manually! It contains just a subset of strings
 * needed during the very first steps of installation. This file was
 * generated automatically by export-installer.php (which is part of AMOS
 * {@link http://docs.moodle.org/dev/Languages/AMOS}) using the
 * list of strings defined in /install/stringnames.txt.
 *
 * @package   installer
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['admindirname'] = 'Dossier administration';
$string['availablelangs'] = 'Paquetages de langue disponibles';
$string['chooselanguagehead'] = 'Choisissez une langue';
$string['chooselanguagesub'] = 'Veuillez choisir la langue d\'installation. Cette langue sera utilisée comme langue par défaut du site, que vous pourrez modifier ultérieurement.';
$string['clialreadyconfigured'] = 'Le fichier config.php existe déjà. Veuillez utiliser admin/cli/install_database.php pour installer Moodle sur ce site.';
$string['clialreadyinstalled'] = 'Le fichier config.php existe déjà. Veuillez utiliser admin/cli/install_database.php si vous désirez mettre à jour ce site Moodle.';
$string['cliinstallheader'] = 'Programme d\'installation de Moodle {$a} en ligne de commande';
$string['databasehost'] = 'Serveur de base de données';
$string['databasename'] = 'Nom de la base de données';
$string['databasetypehead'] = 'Sélectionner un pilote de base de données';
$string['dataroot'] = 'Dossier de données';
$string['datarootpermission'] = 'Droits d\'accès au dossier de données';
$string['dbprefix'] = 'Préfixe des tables';
$string['dirroot'] = 'Dossier Moodle';
$string['environmenthead'] = 'Vérification de l\'environnement...';
$string['environmentsub2'] = 'Chaque version de Moodle nécessite une version minimale de certains composants PHP et des extensions de PHP obligatoires. Une vérification complète de l\'environnement est effectuée avec chaque installation et chaque mise à jour. Veuillez contacter l\'administrateur du serveur si vous ne savez pas comment installer une nouvelle version ou activer des extensions de PHP.';
$string['errorsinenvironment'] = 'Échec de la vérification de l\'environnement !';
$string['installation'] = 'Installation';
$string['langdownloaderror'] = 'La langue {$a} n\'a pas pu être téléchargée. La suite de l\'installation se déroulera en anglais. Vous pourrez télécharger et installer d\'autres langues à la fin de l\'installation';
$string['memorylimithelp'] = '<p>La limite de mémoire de PHP sur votre serveur est actuellement de {$a}.</p>
<p>Cette valeur trop basse risque de générer des problèmes de manque de mémoire pour Moodle, notamment si vous utilisez beaucoup de modules et/ou si vous avez un grand nombre d\'utilisateurs.</p>
<p>Il est recommandé de configurer PHP avec une limite de mémoire aussi élevée que possible, par exemple 40 Mo. Vous pouvez obtenir cela de différentes façons :</p>
<ol>
<li>si vous en avez la possibilité, recompilez PHP avec l\'option <em>--enable-memory-limit</em>. Cela permettra à Moodle de fixer lui-même sa limite de mémoire ;</li>
<li>si vous avez accès à votre fichier « php.ini », vous pouvez attribuer au paramètre <b>memory_limit</b> une valeur comme 40M. Si vous n\'y avez pas accès, demandez à l\'administrateur de le faire pour vous ;</li>
<li>sur certains serveurs, vous pouvez créer dans le dossier principal de Moodle un fichier « .htaccess » contenant cette ligne :
<blockquote><div>php_value memory_limit 40M</div></blockquote>
<p>Cependant, sur certains serveurs, cela empêchera le fonctionnement correct de <b>tous</b> les fichiers PHP (vous verrez s\'afficher des erreurs lors de la consultation de pages). Dans ce cas, vous devrez supprimer le fichier « .htaccess ».</p></li>
</ol>';
$string['paths'] = 'Chemins';
$string['pathserrcreatedataroot'] = 'Le dossier de données ({$a->dataroot}) ne peut pas être créé par l\'installeur.';
$string['pathshead'] = 'Confirmer les chemins d\'accès';
$string['pathsrodataroot'] = 'Le dossier de données n\'est pas accessible en écriture.';
$string['pathsroparentdataroot'] = 'Le dossier parent ({$a->parent}) n\'est pas accessible en écriture. Le dossier de données ({$a->dataroot}) ne peut pas être créé par l\'installeur.';
$string['pathssubadmindir'] = 'Quelques rares hébergeurs utilisent « /admin » comme URL spéciale pour l\'accès à un tableau de bord ou d\'autres fonctionnalités. Malheureusement ceci entre en conflit avec l\'emplacement standard des pages d\'administration de Moodle. Vous pouvez corriger ceci en renommant le dossier admin de votre installation Moodle et en plaçant le nouveau nom choisi dans ce champ. Par exemple, <em>moodleadmin</em>. Ceci modifiera tous les liens de l\'administration de Moodle.';
$string['pathssubdataroot'] = '<p>Un dossier dans lequel Moodle stockera tous les fichiers qui seront déposés par les utilisateurs.</p>
<p>Ce dossier doit être accessible en lecture et en écriture par le serveur web (dénomé « nobody » ou « apache » ou encore « www-data »).</p>
<p>Il ne doit pas être accessible directement via le web.</p>
<p>Si ce dossier n\'existe pas encore, Moodle tentera de le créer au cours du processus d\'installation.</p>';
$string['pathssubdirroot'] = '<p>Le chemin d\'accès complet au dossier contenant le code source de Moodle.</p>';
$string['pathssubwwwroot'] = '<p>L\'adresse web complète par laquelle on accédera à Moodle, i.e. l\'adresse que les utilisateurs saisiront dans la barre d\'adresse de leur navigateur pour accéder à Moodle.</p>
<p>Il n\'est pas possible d\'accéder à Moodle depuis plusieurs adresses web différentes. Si votre site web est accessible depuis plusieurs adresses, saisissez ici la plus simple d\'entre elles et définissez des redirections permanentes pour toutes les autres adresses.</p>
<p>Si votre site est accessible depuis internet et un réseau interne (un intranet), indiquez ici l\'adresse publique.</p>
<p>Si l\'adresse indiquée actuellement n\'est pas correcte,  veuillez modifier l\'URL dans la barre d\'adresse de votre navigateur et recommencer l\'installation.</p>';
$string['pathsunsecuredataroot'] = 'L\'emplacement du dossier de données n\'est pas sûr';
$string['pathswrongadmindir'] = 'Le dossier d\'administration n\'existe pas';
$string['phpextension'] = 'Extension PHP {$a}';
$string['phpversion'] = 'Version de PHP';
$string['phpversionhelp'] = '<p>Moodle nécessite au minimum la version 5.6.5 (7.x a des limitations avec certains moteurs de base de données).</p><p>Vous utilisez actuellement la version {$a}.</p><p>Veuillez mettre à jour PHP ou passer à un hébergement avec une version plus récente de PHP.</p>';
$string['welcomep10'] = '{$a->installername} ({$a->installerversion})';
$string['welcomep20'] = 'Vous voyez cette page, car vous avez installé Moodle correctement et lancé le logiciel <b>{$a->packname} {$a->packversion}</b> sur votre ordinateur. Félicitations !';
$string['welcomep30'] = 'Cette version de <b>{$a->installername}</b> comprend des logiciels qui créent un environnement dans lequel <b>Moodle</b> va fonctionner, à savoir :';
$string['welcomep40'] = 'Ce paquet contient également <b>Moodle {$a->moodlerelease} ({$a->moodleversion})</b>.';
$string['welcomep50'] = 'L\'utilisation de tous les logiciels de ce paquet est soumis à l\'acceptation de leurs licences respectives. Le paquet <b>{$a->installername}</b> est un <a href="http://www.opensource.org/docs/definition_plain.html">logiciel libre</a>. Il est distribué sous licence <a href="http://www.gnu.org/copyleft/gpl.html">GPL</a>.';
$string['welcomep60'] = 'Les pages suivantes vous aideront pas à pas à configurer et mettre en place <b>Moodle</b> sur votre ordinateur. Il vous sera possible d\'accepter les réglages par défaut ou, facultativement, de les adapter à vos propres besoins.';
$string['welcomep70'] = 'Cliquer sur le bouton « Suivant » ci-dessous pour continuer l\'installation de <b>Moodle</b>.';
$string['wwwroot'] = 'Adresse web';
