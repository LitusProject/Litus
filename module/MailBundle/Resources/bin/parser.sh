#!/bin/bash

# A very small wrapper around our e-mail parser
#

scriptDirectory=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "${scriptDirectory}/../../"

php bin/MailBundle/parser.php --run
