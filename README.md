# idGettr

Look up a Flickr user's NSID from their username or profile URL.

**[veertje.toolforge.org/idGettr](https://veertje.toolforge.org/idGettr/)**

## Usage

Enter a Flickr username (e.g. `1Veertje`) or profile URL (e.g. `https://www.flickr.com/people/1Veertje/`) and get back the user's NSID. The NSID can be copied to clipboard with one click.

## Development

```sh
# Serve locally (PHP built-in server)
php -S localhost:8080

# Deploy to Toolforge
scp index.php config.php veertje@login.toolforge.org:/data/project/veertje/public_html/idGettr/
```

The Flickr API key lives in `config.php` (gitignored) and is read server-side. API calls are proxied through PHP so the key never reaches the browser.

## License

MIT
