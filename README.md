Bolotweet is a modified install of STATUS.NET with plugins to enable professors to score, report, and create scheduled micro-annotation exercises. The goal of Bolotweet is to achieve a low effort continuous evaluation system. Bolotweet is regularly used in some subjects in the Facultad de Informática of the Universidad Complutense de Madrid.

The work started with support from the Universidad Complutense as a PIMCD (Proyecto de Innovación y Mejora de la Calidad Docente, or Innovation and Enhancement of the Teaching Quality) within three years. The last year, a student grant was used to advance the project and create new plugins and improve the user interface of the prototype system. The modification consists in several plugins incorporated in a 2013 version of status net. The description of the original plugins and the new versions are described in 

http://eprints.ucm.es/30200/1/MemoriaFinal.pdf

This repository has been built using the pre-deployment version of STATUS.NET. Its install procedure should be the same as with STATUS.NET.

STATUS.NET exists no more and it is GNU Social. It is within the following repository. Future versions of this software will integrate with the bleeding edge version of this tool:

https://git.gnu.io/gnu/gnu-social

#Credits

The project is a joint effort of professors and students to bring Bolonia principles into daily teching activities. The participants of the project are:

- Head of the project: Jorge J. Gomez Sanz (jjgomez@ucm.es)
- Developers: Álvaro Ortego (ex-alumni,  alvorteg@ucm.es), Jorge J. Gomez Sanz (jjgomez@ucm.es)
- Involved professors in the evaluation of the system: Juan Pavón (jpavon@ucm.es), Carlos Cervigón (ccervigon@fdi.ucm.es), Jorge J. Gomez Sanz (jjgomez@ucm.es)


#Specific instructions for Status.NET install


StatusNet 1.1.1
16 July 2013

This is the README file for StatusNet, the Open Source social
networking platform. It includes general information about the
software and the project.

Some other files to review:

- INSTALL: instructions on how to install the software.
- UPGRADE: upgrading from earlier versions
- CONFIGURE: configuration options in gruesome detail.
- PLUGINS.txt: how to install and configure plugins.
- EVENTS.txt: events supported by the plugin system
- COPYING: full text of the software license

Information on using StatusNet can be found in the "doc" subdirectory
or in the "help" section on-line.

About
=====

StatusNet is a Free and Open Source social networking platform. It
helps people in a community, company or group to exchange short status
updates, do polls, announce events, or other social activities (and
you can add more!). Users can choose which people to "follow" and
receive only their friends' or colleagues' status messages. It
provides a similar service to sites like Twitter, Google+, Facebook or
Yammer, but is much more awesome.

With a little work, status messages can be sent to mobile phones,
instant messenger programs (GTalk/Jabber), and specially-designed
desktop clients that support the Twitter API.

StatusNet supports an open standard called OStatus
<http://ostatus.org/> that lets users in different networks follow
each other. It enables a distributed social network spread all across
the Web.

StatusNet was originally developed for the Open Software Service,
Identi.ca <http://identi.ca/>. It is shared with you in hope that you
too make an Open Software Service available to your users. To learn
more, please see the Open Software Service Definition 1.1:

    http://www.opendefinition.org/ossd

StatusNet, Inc. <http://status.net/> also offers this software as a
Web service, requiring no installation on your part. See
<http://status.net/signup> for details. The software run
on status.net is identical to the software available for download, so
you can move back and forth between a hosted version or a version
installed on your own servers.

A commercial software subscription is available from StatusNet Inc. It
includes 24-hour technical support and developer support. More
information at http://status.net/contact or email sales@status.net.

License
=======

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public
License along with this program, in the file "COPYING".  If not, see
<http://www.gnu.org/licenses/>.

    IMPORTANT NOTE: The GNU Affero General Public License (AGPL) has
    *different requirements* from the "regular" GPL. In particular, if
    you make modifications to the StatusNet source code on your server,
    you *MUST MAKE AVAILABLE* the modified version of the source code
    to your users under the same license. This is a legal requirement
    of using the software, and if you do not wish to share your
    modifications, *YOU MAY NOT INSTALL STATUSNET*.

Documentation in the /doc-src/ directory is available under the
Creative Commons Attribution 3.0 Unported license, with attribution to
"StatusNet". See http://creativecommons.org/licenses/by/3.0/ for details.

CSS and images in the /theme/ directory are available under the
Creative Commons Attribution 3.0 Unported license, with attribution to
"StatusNet". See http://creativecommons.org/licenses/by/3.0/ for details.

Our understanding and intention is that if you add your own theme that
uses only CSS and images, those files are not subject to the copyleft
requirements of the Affero General Public License 3.0. See
http://wordpress.org/news/2009/07/themes-are-gpl-too/ . This is not
legal advice; consult your lawyer.

Additional library software has been made available in the 'extlib'
directory. All of it is Free Software and can be distributed under
liberal terms, but those terms may differ in detail from the AGPL's
particulars. See each package's license file in the extlib directory
for additional terms.

New this version
================

This is a security fix and bug fix release since 1.1.0,
released 2 July 2012. All 1.1.0 sites should upgrade to this version.

It includes the following changes:

- Fixes for SQL injection errors in profile lists.
- Improved ActivityStreams JSON representation of activities and objects.
- Upgrade to the Twitter 1.1 API.
- More robust handling of errors in distribution.
- Fix error in OStatus subscription for remote groups.
- Fix error in XMPP distribution.

A full changelog is available at http://status.net/wiki/StatusNet_1.1.1.

Troubleshooting
===============

The primary output for StatusNet is syslog, unless you configured a
separate logfile. This is probably the first place to look if you're
getting weird behaviour from StatusNet.

If you're tracking the unstable version of StatusNet in the git
repository (see below), and you get a compilation error ("unexpected
T_STRING") in the browser, check to see that you don't have any
conflicts in your code.

Unstable version
================

If you're adventurous or impatient, you may want to install the
development version of StatusNet. To get it, use the git version
control tool <http://git-scm.com/> like so:

    git clone git@gitorious.org:statusnet/mainline.git

This is the version of the software that runs on Identi.ca and the
status.net hosted service. Using it is a mixed bag. On the positive
side, it usually includes the latest security and bug fix patches. On
the downside, it may also include changes that require admin
intervention (like running a script or even raw SQL!) that may not be
documented yet. It may be a good idea to test this version before
installing it on your production machines.

To keep it up-to-date, use 'git pull'. Watch for conflicts!

Further information
===================

There are several ways to get more information about StatusNet.

* There is a mailing list for StatusNet developers and admins at
  http://mail.status.net/mailman/listinfo/statusnet-dev
* The #statusnet IRC channel on freenode.net <http://www.freenode.net/>.
* The StatusNet wiki, http://status.net/wiki/
* The StatusNet blog, http://status.net/blog/
* The StatusNet status update, <http://status.status.net/> (!)

Feedback
========

* Messages to http://support.status.net/ are very welcome.
* The group http://identi.ca/group/statusnet is a good
  place to discuss the software.
* StatusNet has a bug tracker for any defects you may find, or ideas for
  making things better. http://status.net/open-source/issues
* The StatusNet forum is at http://forum.status.net/.

Credits
=======

The following is an incomplete list of developers who've worked on
StatusNet. Apologies for any oversight; please let evan@status.net know
if anyone's been overlooked in error.

* Evan Prodromou, founder and lead developer, StatusNet, Inc.
* Zach Copley, StatusNet, Inc.
* Earle Martin, StatusNet, Inc.
* Marie-Claude Doyon, designer, StatusNet, Inc.
* Sarven Capadisli, StatusNet, Inc.
* Robin Millette, StatusNet, Inc.
* Ciaran Gultnieks
* Michael Landers
* Ori Avtalion
* Garret Buell
* Mike Cochrane
* Matthew Gregg
* Florian Biree
* Erik Stambaugh
* 'drry'
* Gina Haeussge
* Tryggvi Björgvinsson
* Adrian Lang
* Ori Avtalion
* Meitar Moscovitz
* Ken Sheppardson (Trac server, man-about-town)
* Tiago 'gouki' Faria (i18n manager)
* Sean Murphy
* Leslie Michael Orchard
* Eric Helgeson
* Ken Sedgwick
* Brian Hendrickson
* Tobias Diekershoff
* Dan Moore
* Fil
* Jeff Mitchell
* Brenda Wallace
* Jeffery To
* Federico Marani
* Craig Andrews
* mEDI
* Brett Taylor
* Brigitte Schuster
* Siebrand Mazeland and the amazing volunteer translators at translatewiki.net
* Brion Vibber, StatusNet, Inc.
* James Walker, StatusNet, Inc.
* Samantha Doherty, designer, StatusNet, Inc.

Thanks also to the developers of our upstream library code and to the
thousands of people who have tried out Identi.ca, installed StatusNet,
told their friends, and built the Open Microblogging network to what
it is today.
