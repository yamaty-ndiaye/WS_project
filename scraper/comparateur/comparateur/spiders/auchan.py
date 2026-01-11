import scrapy

class AuchanSpider(scrapy.Spider):
    name = "auchan"
    allowed_domains = ["auchan.sn"]
    
    start_urls = ["https://www.auchan.sn/137-boissons"]

    def parse(self, response):
        products = response.css('article.product-miniature')
        
        for product in products:
            yield {
                # On récupère le titre dans le <h2> 
                'title': product.css('h2.product-title a::text').get().strip(),
                
                # On récupère le prix dans la span "price"
                'price': product.css('span.price::text').get().strip(),
                
                # Image et URL
                'image_url': product.css('img::attr(src)').get(),
                'url': product.css('h2.product-title a::attr(href)').get(),
                'source': 'auchan'
            }

        # GESTION DE LA PAGINATION
        next_page = response.css('a.next::attr(href)').get()
        if next_page:
            yield response.follow(next_page, self.parse)