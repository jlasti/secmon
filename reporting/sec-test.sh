#!/bin/bash




# farby pre vypis do konzoly
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
RED='\033[0;31m'
NC='\033[0m'




# funkcia pre kontrolu, či je program nainštalovaný
check_programs() {
    for program in "semgrep" "nmap" "nikto" "sqlmap" "xdg-open" "jq"
        do
            if ! command -v "$program" &> /dev/null
            then
                echo -e "${RED}$program is not installed.${NC}"
            else
                echo -e "${YELLOW}$program is installed.${NC}"
            fi
        done
}




# funkcia pre nainštalovanie programov
install_programs() {
    echo -e "${YELLOW}Installing missing programs...${NC}"
    for program in "semgrep" "nmap" "nikto" "sqlmap" "xdg-open" "jq"
    do
        if ! command -v "$program" &> /dev/null
        then
            sudo apt-get install "$program"
            echo -e "${YELLOW}$program newly installed.${NC}"
        else
            echo -e "${YELLOW}$program already installed.${NC}"
        fi
    done
}


# funkcia pre spustenie programov na danú IP adresu
run_programs() {
    echo -e "${YELLOW}Enter IP address to test:${NC} (e.g. 127.0.0.1)"
    read ip
    echo -e "${YELLOW}Enter port number:${NC} (e.g. 8080)"
    read port
    echo -e "${YELLOW}Enter web root directory location:${NC} (e.g. secmon/web)"
    read root_dir
    echo -e "${YELLOW}Enter full path to directory:${NC} (e.g. /home/secmon/secmon)"
    read dir_path
    echo -e "${CYAN}Enter file name to save the test output:${NC} (e.g. report)"
    read file


    # vytvorenie report suboru
    time=$(date +"%d.%m. %Y %H:%M:%S CET")
    echo "<html><head><title>Security chceck result</title>" > $file.html
    echo "<style>h1 {background-color: #3C99DC; color: white; text-align: center; margin: 0 -5%} body {margin: 0 2%} h2 {color: #3C99DC}</style></head>" >> $file.html
    echo "<h1>Automated Security-Scan</h1>" >> $file.html
    echo "<aside style='right:0; position:fixed; background-color: white; margin: 0 2% 0 0'><h2>Table of contents</h2><ul><li><a href='#info'>Scan info</a></li><li><a href='#nmap'>Nmap scan results</a></li><li><a href='#semgrep'>Semgrep scan results</a></li><li><a href='#nikto'>Nikto scan results</a></li></ul></aside>" >> $file.html
    echo "<body><h2 id='info'>Scan info</h2><ul><li>IP address: $ip</li><li>port number: $port</li><li>root directory (index): $root_dir</li><li>source file location: $dir_path</li><li>time: $time</li></ul>" >> $file.html


    echo ""
    echo -e "${RED}STARTING SCANS - please be patient, this may take a while${NC}"
    echo ""


    # nmap ---------------------
    echo -e "${YELLOW}Running nmap to discover open ports and service vulnerabilities...${NC}"
    echo "<h2 id='nmap'>Nmap results</h2>" >> $file.html
    echo "<h3>Nmap scan 1</h3>" >> $file.html
    nmap -sn $ip | awk '/Nmap scan report/{printf $5;}/MAC Address:/{print " => "$3;}' | sort | sed 's/$/<br>/g' >> $file.html
    echo "<br>" >> $file.html
    nmap -sV -vv -T5 $ip | sed 's/$/<br>/g' >> $file.html
    echo "<br>" >> $file.html
    echo "<h3>Nmap scan 2</h3>" >> $file.html
    nmap -A -sV --script vuln -p $port $ip | sed 's/$/<br>/' >> $file.html


    # semgrep ---------------------
    echo ""
    echo -e "${YELLOW}Running semgrep to execute static code analysis...${NC}"
    echo "<h2 id='semgrep'>Semgrep results</h2>" >> $file.html
    LANG=en_US.UTF-8 semgrep --config auto "$dir_path" | sed -r "s/\x1B\[([0-9]{1,2}(;[0-9]{1,2})?)?[mGK]//g; s/$/<br>/g" >> $file.html


    # nikto ---------------------
    echo ""
    echo -e "${YELLOW}Running nikto to scan web information...${NC}"
    echo "<h2 id='nikto'>Nikto results</h2>" >> $file.html
    nikto -h "http://$ip:$port/$root_dir" -C all | sed 's/$/<br>/g' >> $file.html


    # final results
    echo "</body></html>" >> $file.html
    echo ""
    echo -e "${CYAN}All tests complete. Results saved to $file.html ${NC}"
}




# funkcia pre vypísanie menu a volanie ostatných funkcií
show_menu() {
    echo ""
    echo "----------- automated web app security testing -----------"
    echo -e "${CYAN}                            MENU${NC}"
    echo ""
    echo -e "${YELLOW}1. Check if required programs are installed.${NC}"
    echo -e "${YELLOW}2. Install missing programs.${NC}"
    echo -e "${YELLOW}3. Run programs on a given IP address.${NC}"
    echo -e "${YELLOW}4. Exit program.${NC}"
    echo "----------------------------------------------------------"
    echo ""


    echo -e "${CYAN}Enter your choice [1-4]:${NC}"
    read choice


    case $choice in
        1)
            echo ""
            check_programs
            ;;
        2)
            echo ""
            install_programs
            ;;
        3)
            echo ""
            run_programs
            ;;
        4)
            echo -e "${CYAN}Turning off...${NC}"
            exit 0
            ;;
        *)
            echo ""
            echo -e "${RED}Invalid option. Please enter a number between 1 and 4.${NC}"
            ;;
    esac


    show_menu
}


show_menu


#




