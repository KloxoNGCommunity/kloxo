rsa-key-size = 2048
#rsa-key-size = 4096
#server = https://acme-v01.api.letsencrypt.org
server = https://acme-staging.api.letsencrypt.org
test-cert = <php echo tested; ?>
renew-by-default = True
email = <?php echo $email; ?>

domains = <?php echo $domainlist; ?>

agree-dev-preview = True
agree-tos = True
debug = False
verbose = False
text = True
authenticator = standalone
standalone-supported-challenges = http-01
http-01-port = 60080
tls-sni-01-port = 60443
#authenticator = webroot
#webroot-path = <?php echo $root_path; ?>
