# This is a basic VCL configuration file for varnish.  See the vcl(7)
# man page for details on VCL syntax and semantics.
# 

# Define the internal network subnet.
# These are used below to allow internal access to certain files while not
# allowing access from the public internet.
acl internal {
  "127.0.0.1";
}

# Default backend definition.  Set this to point to your content
# server.
# 
backend default {
  .host = "127.0.0.1";
  .port = "80";
# enable to use status script for health of backend 
#  .probe = { .url = "/status.php"; .interval = 5s; .timeout = 1s; .window = 5;.threshold = 3; }
}

#
# BOOSTED VARNISH - by Trellon, LLC
# 
# - Based on a configuration by Lullabot and VCL examples for Varnish Wiki.
#
# Logic is:
# 
# * Assume: A cookie is set (we add __varnish=1 to client request to make this always true)
# * If boosted URL -> unset cookie
# * If backend not healthy -> unset cookie
# * If graphic or CSS file -> unset cookie
# 
# Backend response:
# 
# * If no (SESSION) cookie was send in, don't allow a cookie to go out, because
#   this would overwrite the current SESSION.

# Respond to incoming requests.
sub vcl_recv {

### START BOOST Logic (Prerequisite)
  # We always send a cookie, so that empty requests are
  # handled by below boost logic, too.
  set req.http.Cookie = ";__varnish=1;" req.http.Cookie;
### END BOOST Logic (Prerequisite)

# Enable to disable varnish
#      return (pipe);

### START DEFAULT RULES

  if (req.http.x-forwarded-for) {
    set req.http.X-Forwarded-For =
    req.http.X-Forwarded-For ", " client.ip;
  } else {
    set req.http.X-Forwarded-For = client.ip;
  }

 if (req.request != "GET" &&
    req.request != "HEAD" &&
    req.request != "PUT" &&
    req.request != "POST" &&
    req.request != "TRACE" &&
    req.request != "OPTIONS" &&
    req.request != "DELETE") {
      # Non-RFC2616 or CONNECT which is weird.
      return (pipe);
  }
  if (req.request != "GET" && req.request != "HEAD") {
    # We only deal with GET and HEAD by default
    return (pass);
  }

### END DEFAULT RULES

  # Allow the backend to serve up stale content if it is responding slowly.
  set req.grace = 6h;

  # Do not cache these paths.
  if (req.url ~ "^/status\.php$" ||
      req.url ~ "^/update\.php$" ||
      req.url ~ "^/ooyala/ping$" ||
      req.url ~ "^/admin/build/features" ||
      req.url ~ "^/info/.*$" ||
      req.url ~ "^/flag/.*$" ||
      req.url ~ "^.*/ajax/.*$" ||
      req.url ~ "^.*/ahah/.*$") {
       return (pass);
  }

  # Pipe these paths directly to Apache for streaming.
  if (req.url ~ "^/admin/content/backup_migrate/export") {
    return (pipe);
  }

  # Do not allow outside access to cron.php or install.php.
  if (req.url ~ "^/(cron|install)\.php$" && !client.ip ~ internal) {
    # Have Varnish throw the error directly.
    error 404 "Page not found.";
    # Use a custom error page that you've defined in Drupal at the path "404".
    # set req.url = "/404";
  }

  # Handle compression correctly. Different browsers send different
  # "Accept-Encoding" headers, even though they mostly all support the same
  # compression mechanisms. By consolidating these compression headers into
  # a consistent format, we can reduce the size of the cache and get more hits.=
  # @see: http:// varnish.projects.linpro.no/wiki/FAQ/Compression
  if (req.http.Accept-Encoding) {
    if (req.url ~ "\.(jpg|png|gif|gz|tgz|bz2|tbz|mp3|ogg|mp4|flv|f4v)$") {
      # No point in compressing these
      remove req.http.Accept-Encoding;
    }
    else if (req.http.Accept-Encoding ~ "gzip") {
      # If the browser supports it, we'll use gzip.
      set req.http.Accept-Encoding = "gzip";
    }
    else {
      # Unknown or deflate algorithm. Remove it and send unencoded.
      unset req.http.Accept-Encoding;
    }
  }

### START .htaccess rewrite rules

# Strip out Google Analytics campaign variables. They are only needed
# by the javascript running on the page
# utm_source, utm_medium, utm_campaign, gclid
  if(req.url ~ "(\?|&)(gclid|utm_[a-z]+)=") {
    set req.url = regsuball(req.url, "(gclid|utm_[a-z]+)=[^\&]+&?", "");
    set req.url = regsub(req.url, "(\?|&)$", "");
  }
 
### END .htaccess rewrite rules
  
### START BOOST Logic

  # This is no pressflow, so we always have a SESSION

  # but boost will always set the DRUPAL_UID cookie
  # when the user is logged in.
  # 
  # So it is safe to remove all cookies for the boosted paths.
  if (!(req.http.Cookie ~ "DRUPAL_UID=")) {
     # User is anonymous

     # Boost rules from .htaccess
     if (!(req.url ~ "(^/(admin|cache|misc|modules|sites|system|openid|themes|node/add))|(/(comment/reply|edit|user|user/(login|password|register))$)")) {

##### BOOST CONFIG: Change this to your needs
       # Boost rules from boost configuration
       if (!(req.url ~ "/(excluded-url-to-be-configured)")) {
         unset req.http.Cookie;
       }
##### END BOOST CONFIG: Change this to your needs
    }
  }

### END BOOST LOGIC

### Cookie removal logic

  # Always cache the following file types for all users.
  if (req.url ~ "(?i)\.(png|gif|jpeg|jpg|ico|swf|css|js|html|htm|gz|tgz|bz2|tbz|mp3|ogg|mp4|flv|f4v|pdf)(\?[a-z0-9]+)?$") {
    unset req.http.Cookie;
  }

  # Use anonymous, cached pages if all backends are down.
  if (!req.backend.healthy) {
    unset req.http.Cookie;
  }

### START LULLABOT / Pressflow Logic

#  # Remove all cookies that Drupal doesn't need to know about. ANY remaining
#  # cookie will cause the request to pass-through to Apache. For the most part
#  # we always set the NO_CACHE cookie after any POST request, disabling the
#  # Varnish cache temporarily. The session cookie allows all authenticated users
#  # to pass through as long as they're logged in.
#  if (req.http.Cookie) {
#    set req.http.Cookie = ";" req.http.Cookie;
#    set req.http.Cookie = regsuball(req.http.Cookie, "; +", ";");
#    set req.http.Cookie = regsuball(req.http.Cookie, ";(SESS[a-z0-9]+|NO_CACHE)=", "; \1=");
#    set req.http.Cookie = regsuball(req.http.Cookie, ";[^ ][^;]*", "");
#    set req.http.Cookie = regsuball(req.http.Cookie, "^[; ]+|[; ]+$", "");
#
#    if (req.http.Cookie == "") {
#      # If there are no remaining cookies, remove the cookie header. If there
#      # aren't any cookie headers, Varnish's default behavior will be to cache
#      # the page.
#      unset req.http.Cookie;
#    }
#    else {
#      # If there is any cookies left (a session or NO_CACHE cookie), do not
#      # cache the page. Pass it on to Apache directly.
#      return (pass);
#    }
#  }

### END LULLABOT / Pressflow Logic


}

# Routine used to determine the cache key if storing/retrieving a cached page.
sub vcl_hash {
  # Include cookie in cache hash.
  # This check is unnecessary because we already pass on all cookies.
  # if (req.http.Cookie) {
  #   set req.hash += req.http.Cookie;
  # }
}

# Code determining what to do when serving items from the Apache servers.
sub vcl_fetch {

### START BOOST Logic

  # This is no pressflow, so we always have a SESSION
  # but we only set SESSION if we actually send cookies
  #
  # So logic is: If we did not send a cookie in,
  # we do not allow a cookie out.

  if (!req.http.Cookie) {
    unset beresp.http.set-cookie;
  }

### END BOOST LOGIC

  # Don't allow static files to set cookies.
  if (req.url ~ "(?i)\.(png|gif|jpeg|jpg|ico|swf|css|js|html|htm|gz|tgz|bz2|tbz|mp3|ogg|mp4|flv|f4v|pdf)(\?[a-z0-9]+)?$") {
    # beresp == Back-end response from the web server.
    unset beresp.http.set-cookie;
  }

  # Allow items to be stale if needed.
  set beresp.grace = 6h;

  # Serve the good page from the cache and re-check in one minute 

  if (beresp.status == 503 || beresp.status == 501 || beresp.status == 500) {
    set beresp.grace = 60s;
    restart;
  }
  
  # Anything that is cacheable, but has expiration date too low 
  # which prevents caching gets cached by varnish to take the 
  # load off apache.

  if (beresp.cacheable && beresp.ttl < 1h) {

##### MINIMUM CACHE LIFETIME: Change this to your needs
    # Set how long Varnish will keep it
    set beresp.ttl = 1h;
##### END MINIMUM CACHE LIFETIME: Change this to your needs

    # marker for vcl_deliver to reset Age:
    set beresp.http.magicmarker = "1";
  }

### START DEBUG

  set beresp.http.X-Varnish-Debug-TTL = beresp.ttl;

#  # Varnish determined the object was not cacheable
#  if (!beresp.cacheable) {
#      set beresp.http.X-Cacheable = "NO:Not Cacheable";
#  # You don't wish to cache content for logged in users
#  } elsif (req.http.Cookie ~ "(UserID|_session)") {
#      set beresp.http.X-Cacheable = "NO:Got Session";
#      return(pass);
#  # You are respecting the Cache-Control=private header from the backend
#  } elsif (beresp.http.Cache-Control ~ "private") {
#      set beresp.http.X-Cacheable = "NO:Cache-Control=private";
#      return(pass);
#  # You are extending the lifetime of the object artificially
#  } elsif (beresp.http.magicmarker) {
#      set beresp.http.X-Cacheable = "YES:FORCED";
#  # Varnish determined the object was cacheable
#  } else {
#      set beresp.http.X-Cacheable = "YES";
#  }
#
### END DEBUG

}

sub vcl_deliver {
### START DEBUG
  if (obj.hits > 0) {
    set resp.http.X-Varnish-Cache = "HIT Varnish (" obj.hits ")";
  }
  else {
    set resp.http.X-Varnish-Cache = "MISS";
  }
  set resp.http.X-Varnish-Debug-Hits  = obj.hits;
  set resp.http.X-Varnish-Debug-Age = resp.http.age;
### END DEBUG

  # The magic marker is used to reset the age to 0
  # as else the object is older than its ttl.  

  if (resp.http.magicmarker) {
    # Remove the magic marker
    unset resp.http.magicmarker;

    # By definition we have a fresh object
    set resp.http.Age = "0";
  }
}


# In the event of an error, show friendlier messages.
sub vcl_error {
  # Redirect to some other URL in the case of a homepage failure.
  #if (req.url ~ "^/?$") {
  #  set obj.status = 302;
  #  set obj.http.Location = "http://backup.example.com/";
  #}

  # Otherwise redirect to the homepage, which will likely be in the cache.
  set obj.http.Content-Type = "text/html; charset=utf-8";
  synthetic {"
<html>
<head>
  <title>"} obj.status " " obj.response {"</title>
  <style>
    #page {width: 400px; padding: 10px; margin: 20px auto; border: 1px solid black; background-color: #FFF;}
    p {margin-left:20px;}
    body {background-color: #DDD; margin: auto;}
    .error { margin-left: 20px; color: #222; }
  </style>
</head>
<body onload="setTimeout(function() { window.location = '/' }, 5000)">
  <div id="page">
    <h1 class="title">Page Could Not Be Loaded</h1>
    <p>The page you requested is temporarily unavailable.</p>
    <p>We're very sorry, but the page could not be loaded properly. This should be fixed very soon, and we apologize for any inconvenience.</p>
    <hr />
    <p>We're redirecting you to the <a href="/">homepage</a> in 5 seconds.</p>
    <div class="error">(Error: "} obj.status " " obj.response {")</div>
  </div>
</body>
</html>
"};
  return (deliver);
}
