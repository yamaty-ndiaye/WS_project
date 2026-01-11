import scrapy

class ProductItem(scrapy.Item):
    title = scrapy.Field()
    price = scrapy.Field()
    image_url = scrapy.Field()  
    url = scrapy.Field()
    source = scrapy.Field()