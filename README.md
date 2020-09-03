<p align="center"><img src="https://raw.githubusercontent.com/isra00/RMSCatalog/master/web/img/RMSCatalog-logo-vectorized-text.svg" height="200" width="200"></p>

# How it works

RMS Catalog is a simple OPAC (On-Line Public Access Catalog) interface for a **library catalog made with Microsoft Excel**. It is **not** an Integrated Library System (ILS) where you can manage your catalog. Instead, you make your catalog on an Excel spreadsheet and upload it to SRM Catalog to have a nice web interface for browsing the catalog from your local network or Internet. In brief, the librarian uses Excel for managing the catalog, and your library users (patrons) use RMS Catalog to search books in the catalog.

The Excel spreadsheet must be formatted according to a few rules specified below, in order to be parsed by RMS Catalog. The best thing is getting our [skeleton Excel catalog](https://github.com/isra00/RMSCatalog/blob/master/Skeleton%20catalog.xlsx) and start working with it.

In addition to the book data recorded in the Excel catalog, RMS Catalog automatically downloads other book metadata as well as the book cover to show it on the record page.

RMS Catalog does not implement circulation management (loans, patrons, fines...)

# Install

1. Clone the repository and point your web server's document root to the `web` directory.
2. Run `composer install`
3. Rename `config-dist.php` into `config.php` and customize the Excel database columns according to the columns of your Excel catalog, as well as other settings.
4. You may customize the "where to find" template or create your own in templates/your-template.twig, and declare it in `config.php`.
4. Browse to /db and upload your library catalog in Excel format. 
5. Have fun!

# Usage of the Excel catalog

 - Catalog your books on Excel, using our [skeleton Excel file](https://github.com/isra00/RMSCatalog/blob/master/Skeleton%20catalog.xlsx).
 - The Excel file must have to sheets: CATALOG and CLASSIFICATION. 
 - The CATALOG sheet's columns may be customized, as long as you declare them in `config.php`. But bear in mind that new columns you add will not be shown on the record page automatically, nor searched on by the OPAC's search feature.
 - The CLASSIFICATION sheet must keep the columns "Code" and "Label" in the first and second place, respectively. The classes codes must be formatted according to this scheme: [LETTERS][NUMBERS].[NUMBERS].[NUMBERS], etc. for example: `A`, `A1`, `A1.1`, `A1.1.1`, etc.
 - Our skeleton Excel file generates spine labels for printing (on the LABEL column), and has basic duplicate detection on titles and classification codes, as well as some validation rules to help you avoid cataloguing mistakes.
