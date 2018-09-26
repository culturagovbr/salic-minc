if [ -e .git/MERGE_MSG ]; then
	MERGE_INTO_HMG=$(cat .git/MERGE_MSG | grep "Merge branch 'hmg' into $(git branch | grep \* | cut -d ' ' -f2)" | wc -m)
	echo ${MERGE_INTO_HMG}
	if [[ $MERGE_INTO_HMG == "0" ]]; then
		NC='\033[0m'
		echo '
		██╗  ██╗███╗   ███╗ ██████╗██████╗
		██║  ██║████╗ ████║██╔════╝╚════██╗
		███████║██╔████╔██║██║  ███╗ ▄███╔╝
		██╔══██║██║╚██╔╝██║██║   ██║ ▀▀══╝
		██║  ██║██║ ╚═╝ ██║╚██████╔╝ ██╗
		╚═╝  ╚═╝╚═╝     ╚═╝ ╚═════╝  ╚═╝
		'
		echo "\033[0;31mVocê não pode usar o merge do${MERGE_INTO_HMG} hmg para sua branch!${NC}"
		echo "\033[0;32mUse 'git merge --abort' para cancelar o merge!${NC}"
	  exit 1
	fi
fi