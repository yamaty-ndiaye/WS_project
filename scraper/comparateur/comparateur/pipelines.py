import re
import mysql.connector
import socket

class ConversionPipeline:
    def open_spider(self, spider):
        # On teste si on est dans Docker ou en local
        try:
            # Si on peut résoudre 'db', on est dans Docker
            socket.gethostbyname('db')
            db_host = "db"
        except socket.gaierror:
            # Sinon, on est en local (WSL/Terminal)
            db_host = "127.0.0.1"

        self.conn = mysql.connector.connect(
            host=db_host,
            user="user_boissons",
            password="pass_boissons",
            database="db_boissons",
            port=3306
        )
        self.cursor = self.conn.cursor()

    def process_item(self, item, spider):
        # Nettoyage prix
        price_raw = str(item.get('price', '0'))
        price_clean = re.sub(r'[^\d]', '', price_raw)
        item['price'] = int(price_clean) if price_clean else 0

        # Insertion
        sql = "INSERT INTO articles (title, price, image_url, url, source) VALUES (%s, %s, %s, %s, %s)"
        self.cursor.execute(sql, (
            item['title'],
            item['price'],
            item['image_url'],
            item['url'],
            item['source']
        ))
        self.conn.commit()
        return item

    def close_spider(self, spider):
        # Protection au cas où la connexion a échoué
        if hasattr(self, 'cursor'):
            self.cursor.close()
        if hasattr(self, 'conn'):
            self.conn.close()