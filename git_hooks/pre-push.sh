#!/bin/sh

branch=$(git rev-parse --abbrev-ref HEAD)
if [ "$branch" = "hmg" ]; then
echo "=========[ Atualizando ambiente de Homologacao a partir da branch $branch ]========"
bash -c 'sleep 5; curl -X POST jenkins.cultura.gov.br/view/Salic/job/SalicHomologacaoPipeline/buildWithParameters?token=790d6d8175da3e7a3ddce28344678696&containerApplicationEnviroment=development&branch=hmg &' &
fi;

exit 0