# Omekalia

This Omeka S module is part of [BibLibre's Omekalia offer](https://www.biblibre.com/fr/omekalia/)
but can be used freely by anyone.

Its purpose is to install a fixed set of modules and themes that have been
proved to work well together.

## Requirements

* Omeka S >= 4.0.0 (4.1.1 is recommended)
* git

## Quick start

1. [Add the module to Omeka S](https://omeka.org/s/docs/user-manual/modules/#adding-modules-to-omeka-s)
2. From the server where Omeka S is installed execute the following command:

   ```bash
   # Replace $OMEKA_PATH by the path where Omeka S is installed
   php $OMEKA_PATH/modules/Omekalia/data/scripts/sync.php --sync-modules --sync-themes
   ```

   This will download and install all modules specified in file `data/omekalia.json`.
   If some of these modules are already installed, their directory will be
   moved to `$XDG_DATA_HOME/omekalia/archive` (or
   `$HOME/.local/share/omekalia/archive` if `$XDG_DATA_HOME` is not set) before
   installing the new version.

   Note that this does not run the module install or upgrade process. You'll
   have to go to the admin interface to (re-)enable them.

## Features

This module adds a status page to the admin interface (accessible from the
navigation menu) where you can see if some of your modules or themes have
versions different from what is specified in `data/omekalia.json`.

## License

Omekalia is distributed under the GNU General Public License, version 3 (GPLv3).
The full text of this license is given in the `LICENSE` file.
