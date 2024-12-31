FROM alpine/git:2.47.1

WORKDIR /var/www/html

RUN apk add --no-cache openssh

RUN git config --global user.name "Tareq Mahbub"
RUN git config --global user.email "tareqmahbub@gmail.com"

RUN mkdir -p /root/.ssh && \
    ssh-keygen -t ed25519 -C "tareqmahbub@gmail.com" -f /root/.ssh/id_ed25519 -N "" && \
    echo "Add following Public Key on your git service providing platform:" && \
    cat /root/.ssh/id_ed25519.pub

ENTRYPOINT [ "git" ]
