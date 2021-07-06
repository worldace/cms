
class Toast extends HTMLElement{

    timeout = 5000       //何ミリ秒表示するか。0で無限
    closeButton = true   //閉じるボタンを表示するかどうか
    x = 'right'          //トーストの表示位置X。'right','left','center'
    y = 'top'            //トーストの表示位置Y。'top', 'bottom'
    onEnd = function(){} //トーストが消えた後に実行する関数


    connectedCallback(){
        benry(this)

        this.parent    = this.parentElement
        this.dataset.x = this.x
        this.dataset.y = this.y

        if(!this.closeButton){
            this.$closeButton.remove()
        }

        if(this.timeout) {
            this.timer = window.setTimeout(this.close, this.timeout)
        }

        this.reposition()
    }


    $closeButton_click(event){
        event.stopPropagation()
        this.close()
    }


    async close() {
        window.clearTimeout(this.timer)
        this.$closeButton.removeEventListener('click', this.$closeButton_click)
        await this.animate({opacity:0}, 400).finished
        this.remove()
        this.onEnd()
        this.reposition()
    }


    reposition() {
        const margin = 15
        const offset = {
            left  : {top: margin, bottom: margin},
            right : {top: margin, bottom: margin},
            center: {top: margin, bottom: margin},
        }

        for(const el of this.parent.children) {
            if(el.tagName === this.tagName){
                const height = el.offsetHeight
                el.style[el.y] = offset[el.x][el.y] + 'px'
                offset[el.x][el.y] += height + margin
            }
        }
    }


    get html(){
        return `
        <slot></slot>
        <div id="closeButton">&#10006;</div>
        <style>
        :host {
            padding: 12px 20px;
            color: #fff;
            display: inline-block;
            background: linear-gradient(135deg, #73a5ff, #5477f5);
            position: fixed;
            transition: all 0.4s cubic-bezier(0.2, 0.6, 0.4, 1);
            border-radius: 2px;
            text-decoration: none;
            user-select: none;
            z-index: 2147483647;
        }
        :host([data-y="top"]) {
            top: -150px;
        }

        :host([data-y="bottom"]) {
            bottom: -150px;
        }

        :host([data-x="right"]) {
            right: 15px;
        }

        :host([data-x="left"]) {
            left: 15px;
        }

        :host([data-x="center"]) {
            margin-left: auto;
            margin-right: auto;
            left: 0;
            right: 0;
            max-width: fit-content;
        }
        #closeButton {
            cursor: pointer;
            padding: 0 5px;
            position: absolute;
            top: 0;
            right: 0;
            opacity: 0.5;
            font-size: 14px;
            color: currentcolor;
        }
        </style>
        `
    }
}


function benry(self, attr = []){ // https://qiita.com/economist/items/6c923c255f6b4b7bbf84
    self.$ = self.attachShadow({mode:'open'})
    self.$.innerHTML = self.html || ''

    for(const el of self.$.querySelectorAll('[id]')){
        self[`$${el.id}`] = el
    }

    for(const name of Object.getOwnPropertyNames(self.constructor.prototype)){
        if(typeof self[name] !== 'function'){
            continue
        }
        self[name]  = self[name].bind(self)
        const match = name.match(/^(\$.*?)_([^_]+)$/)
        if(match && self[match[1]]){
            self[match[1]].addEventListener(match[2], self[name])
        }
    }

    for(const name of attr){
        self[name] = self.getAttribute(name)
    }
}


function toaster(content, option = {}){
    const toast = document.createElement('butter-toast')

    if(typeof content === 'string'){
        toast.innerHTML = content
    }
    else{
        toast.append(content)
    }

    Object.assign(toast, option)
    document.body.prepend(toast)

    return toast
}


customElements.define('butter-toast', Toast)

export default toaster