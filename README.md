# TestCsrf
PHP tool to test Cross Site Request Forgery aka CSRF.  
Note that this is an automated tool, manual check is still required.  

```
Usage: php testcsrf.php [OPTIONS] -o <token> -f <request_file>

Options:
	-cl	force Content-Length header
	-f	source file of the orignal request
	-h	print this help
	-m	test mode, default all
		0: remove the token
		1: change the value of the token (but keep the length the same)
		2: remove the value of the token (but leave the parameter in place)
		3: convert to a GET request
	-o	token name
	-r	do not follow redirection
	-s	force https
	-t	set tolerance for result output, default=5%

Examples:
	testcsrf.php -o magic_token -f request.txt
	testcsrf.php -r -s -o magic_token -f request.txt
	testcsrf.php -t 10 -m 1 -o magic_token -f request.txt
```

I don't believe in license.  
You can do want you want with this program.  
