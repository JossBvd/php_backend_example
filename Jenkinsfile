pipeline {
    agent none
    stages {
        stage('Clone repository') {
            agent { label 'agent-php' }
            steps {
                git branch: 'main', url: 'https://github.com/JossBvd/php_backend_example'
            }
        }
        stage('Deploy via FTP') {
            agent { label 'agent-lftp' }
            steps {
                sh '''
                    lftp -d -u jocelyn1,n8664f6y ftp-jocelyn1.alwaysdata.net -e "
                        mirror -R /home/jenkins/workspace/jocelyn-test/ www/;
                        bye
                    "
                '''
            }
        }
        stage('Install Composer') {
            agent { label 'agent-php' }
            steps {
                sh """
                    sshpass -p n8664f6y ssh -o StrictHostKeyChecking=no jocelyn1@ssh-jocelyn1.alwaysdata.net '
                        cd ~/www && composer install --no-dev &&
                        echo "HOST=mysql-jocelyn1.alwaysdata.net" > .env &&
                        echo "DBNAME=jocelyn1_db" >> .env &&
                        echo "USERNAME=[CHANGE_ME]" >> .env &&
                        echo "PASSWORD=[CHANGE_ME]" >> .env
                    '
                """
            }
        }
    }
}
