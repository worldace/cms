
class MyTab extends HTMLElement{
    connectedCallback(){
        benry(this)

        for(const el of this.children){
            this.$tab.insertAdjacentHTML('beforeend', `<li>${el.dataset.title}</li>`)
        }
    }


    $tab_click(event){
        if(event.target.tagName === 'LI'){
            this.open(Array.from(this.$tab.children).indexOf(event.target))
        }
    }


    open(index = 0){
        const target = this.children[index]

        for(const el of this.children){
            el.dataset.selected = el === target
        }

        target.dispatchEvent(new CustomEvent('tabopen', {detail:index,bubbles:true}))
    }


    get html(){
        return `
        <ul id="tab"></ul>
        <slot id="slot"></slot>
        <style>
        :host{
            display: grid;
            grid-template: "tab content" 100vh / 200px 1fr;
        }
        #tab{
            padding:0;
            margin:0;
            grid-area: tab;
            background-color: #383838;
        }
        #tab li{
            color: #fff;
            padding: 10px;
            display: block;
            cursor: pointer;
            font-size: 18px;
            border-bottom: 1px solid #aaa;
            background-image: -webkit-linear-gradient(top, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.3));
        }
        #tab li:hover{
            color: #8cf;
        }
        #slot{
            grid-area: content;
            border:solid 1px #000;
        }
        ::slotted(section){
            display: none;
        }
        ::slotted(section[data-selected]){
            display: block;
        }
        ::slotted(section[data-selected="false"]){
            display: none;
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


customElements.define('my-tab', MyTab)
