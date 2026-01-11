# settings.py

BOT_NAME = "comparateur"

SPIDER_MODULES = ["comparateur.spiders"]
NEWSPIDER_MODULE = "comparateur.spiders"

# --- IDENTIFICATION ---
USER_AGENT = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"

# --- POLITESSE ---
ROBOTSTXT_OBEY = False
DOWNLOAD_DELAY = 1
CONCURRENT_REQUESTS_PER_DOMAIN = 1
COOKIES_ENABLED = False

# --- EXPORT ---
FEED_EXPORT_ENCODING = "utf-8"
LOG_LEVEL = "INFO"

# --- ACTIVATION DE LA PIPELINE (LE PLUS IMPORTANT) ---
ITEM_PIPELINES = {
    "comparateur.pipelines.ConversionPipeline": 300,
}