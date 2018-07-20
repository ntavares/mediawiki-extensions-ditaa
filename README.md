# mediawiki-extensions-ditaa

Ditaa extension for Mediawiki

Abstract
========

I've recently had to port this extension for a customer, from a mediawiki 1.19 to 1.30. Somewhere along the way, the extension registration changed severely, so this extension was not working anymore. As I found it, it was not really an extension (there is no [Extension:Ditaa](https://www.mediawiki.org/wiki/Extension:Ditaa) at [mediawiki.org](mediawiki.org)), and the author was barely identified. The refactoring is actually bigger than the rendering, but nevertheless, it is not my intention to take over any credits and therefore I'm uploading the original version.

Installation
========
(TO IMPROVE)
As of Mediawiki 1.30 (and possibly earlier versions), it should be enough to drop the Extension folder in the extensions/ folder, and register it in LocalSettings.php with:
```
wfLoadExtension('Ditaa');
$wgDitaaCommand = "/usr/local/bin/wiki_ditaa.sh";
// ditaa was seen eating up ~2GB of memory
$wgMaxShellMemory = 3000000;
```

The wiki_ditaa.sh script is just a wrapper (included) to  ditaa, which you can get from [here](http://ufpr.dl.sourceforge.net/project/ditaa/ditaa/0.9/ditaa0_9.zip).

Support
========
Beware that I might not be able to support this extension as much as desired. You're more than welcome to submit Pull Requests.
