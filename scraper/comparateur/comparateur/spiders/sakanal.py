import scrapy

class SakanalSpider(scrapy.Spider):
    name = "sakanal"
    start_urls = ["https://sakanal.sn/fr/recherche?controller=search&s=boissons"]

    def parse(self, response):
        # On cible chaque bloc produit
        for product in response.css("article.product-miniature"):
            
            # --- TITRE ET URL ---
            # On cherche le lien dans les classes de titres courantes
            link_tag = product.css(".product-title a, .productName a, h3 a, h2 a")
            title = link_tag.css("::text").get()
            raw_url = link_tag.css("::attr(href)").get()

            # --- IMAGE ---
            # On récupère l'image en priorité via data-src (lazy load)
            img_tag = product.css("img")
            image_url = img_tag.css("::attr(data-src)").get() or img_tag.css("::attr(src)").get()

            # --- PRIX ---
            # On cible le span de prix standard
            price = product.css("span.price::text, .product-price::text").get()

            yield {
                'title': title.strip() if title else "Titre non trouvé",
                'price': price.strip() if price else "0",
                'image_url': image_url,
                'url': response.urljoin(raw_url) if raw_url else None,
                'source': 'sakanal'
            }

        # --- PAGINATION ---
        next_page = response.css("a.next.js-search-link::attr(href)").get()
        if next_page:
            yield scrapy.Request(response.urljoin(next_page), callback=self.parse)