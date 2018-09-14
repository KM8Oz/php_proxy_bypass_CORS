## php_proxy_bypass_CORS
#### Cross-Origin Resource Sharing (CORS) bypass by a simple php proxy allowed by free host's 
**example to test with:** kmoz000.000webhostapp.com/proxy/?url=**yours_here**
 - first host the *php* file in folder an changer chmod to 655 or others options that allows the proxy work fine 
 - ### @indexes ($_GET) : 
    - kmoz000.000webhostapp.com/proxy/?url=example.com&type=json
      - ##### return :
      > contents:{tag: "html", children: [{tag: "head", children: [{tag: "title", html: "Index of /Serial/"}]},…]}
      > status:{http_code: 200}
      
    - kmoz000.000webhostapp.com/proxy/?url=example.com&type=html
      - ##### return :
      > <html>
      > <body bgcolor="white">
      > <h1>document html</h1><hr><pre><a href="../">../</a>
      
      - ##### in default return Array => [ ..
#### work with **POST** and **GET** Method    
