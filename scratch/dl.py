import urllib.request
import ssl

url = 'https://cdn.dribbble.com/userupload/44483904/file/6206085c99775e50f8a633058272bb93.png'
dest = r'c:\xampp\htdocs\creda\scratch\dribbble_ref.png'

ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

req = urllib.request.Request(
    url, 
    headers={'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'}
)

with urllib.request.urlopen(req, context=ctx, timeout=15) as response, open(dest, 'wb') as out_file:
    data = response.read()
    out_file.write(data)
    print(f"DONE: {len(data)} bytes")
