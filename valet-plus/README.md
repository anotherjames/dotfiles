Patches in this directory
-------------------------

all.conf file should be copied to ~/.valet/Nginx/all.conf

drush_aliases.php file should be copied into ~/.valet/Extensions/

Installing valet-php Brew packages
----------------------------------

valet-php packages need to be built from source when installing with Brew (e.g. `brew install valet-php@7.3 --build-from-source`) as their ordinary URL [doesn't work any more](https://github.com/henkrehorst/homebrew-php/issues/148). Also, the version of ICU at the time of writing causes `make` errors (this is a [php source issue](https://github.com/php/php-src/pull/7596)), so before doing so, the formula definitions may need editing to use newer patch versions of PHP, and/or apply an actual patch - see https://github.com/henkrehorst/homebrew-php/issues/158. This means running something like `brew edit valet-php@7.3` and changing something like the following (based on [this PR](https://github.com/henkrehorst/homebrew-php/pull/160/files#diff-d678fc2f49bcbc4d2fcb42689ed91b30fb2d0bd4c15a00f64a974019f23717e7)):

```diff
diff --git a/Formula/valet-php@8.0.rb b/Formula/valet-php@8.0.rb
index 3994188..f5d5415 100644
--- a/Formula/valet-php@8.0.rb
+++ b/Formula/valet-php@8.0.rb
@@ -1,8 +1,8 @@
 class ValetPhpAT80 < Formula
   desc "General-purpose scripting language"
   homepage "https://www.php.net/"
-  url "https://www.php.net/distributions/php-8.0.3.tar.xz"
-  sha256 "c9816aa9745a9695672951eaff3a35ca5eddcb9cacf87a4f04b9fb1169010251"
+  url "https://www.php.net/distributions/php-8.0.17.tar.xz"
+  sha256 "4e7d94bb3d144412cb8b2adeb599fb1c6c1d7b357b0d0d0478dc5ef53532ebc5"
 
   bottle do
     root_url "https://dl.bintray.com/henkrehorst/valet-php"
```

The exact SHA hashes can be taken from https://www.php.net/downloads.php. For the PHP versions that don't have a fix themselves (i.e. the older supported ones), patching like the following would be needed (this example is for PHP 7.3, but [patches exist for other versions](https://github.com/henkrehorst/homebrew-php/issues/158#issuecomment-1063425871):

```diff
   def install
+     # From https://github.com/henkrehorst/homebrew-php/issues/158#issuecomment-1063425871
+     system "/usr/bin/curl", "-fsLO", "https://gist.githubusercontent.com/nickolasburr/d24481ded3dcf19d5fc45ccb9892b0cd/raw/8e39f300b24c984183fc24fbc68a6608bf33f832/valet-php@7.3-icu.patch"
+     system "/usr/bin/git", "apply", "-v", "valet-php\@7.3-icu.patch"

     # Ensure that libxml2 will be detected correctly in older MacOS
     ENV["SDKROOT"] = MacOS.sdk_path if MacOS.version == :el_capitan || MacOS.version == :sierra
```

Make sure a valet-php package is properly uninstalled before trying to replace it after these changes - you have to use `brew uninstall valet-php@7.4; brew install valet-php@7.4 --build-from-source` rather than simply `brew reinstall valet-php@7.4 --build-from-source`. You may well also need to run something like `brew unlink valet-php@7.4 && brew link --force valet-php@7.4`. Running `brew services list` (with & without sudo) should show what services are correctly installed & running. I would expect to see the following services running:

| Service name | Sudo or not |
| ------------ | ----------- |
| dnsmasq | sudo (correct?) |
| mailhog | sudo (correct?) |
| mysql | not |
| nginx | sudo |
| solr | not (yet ran as root?) |
| valet-php | sudo (correct?) |

Since my global composer.json now sets the platform PHP version to 7.4, if using an older version of PHP, it can be worth running `composer global install --ignore-platform-req=php` to allow any compatible globally-required packages (e.g. the global drush 8) to be installed/run. (And obviously, switch back to >= 7.4 for global packages that _do_ need at least 7.4.)
