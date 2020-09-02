# How it works
RMS Catalog is a simple OPAC (On-Line Public Access Catalog) interface for a *library catalog made with Microsoft Excel*. It is *not* an Integrated Library System (ILS) where you can manage your catalog. Instead, you make your catalog on an Excel spreadsheet and upload it to SRM Catalog to have a nice web interface for browsing the catalog from your local network or Internet.

The Excel spreadsheet must be formatted according to a few rules, in order to be parsed by RMS Catalog. The best thing is checking our sample catalog and taking a look at its structure.

In addition to the book data recorded in the Excel catalog, RMS Catalog automatically downloads other book metadata as well as the book cover to show it on the record page.

# Install
1. Clone the repository and point your web server's document root to the `web` directory.
2. Run `composer install`
3. Rename `config-dist.php` into `config.php` and customize the Excel database columns according to the columns of your Excel catalog, as well as other settings.
4. Browse to /db and upload your library catalog in Excel format.
5. Have fun!
