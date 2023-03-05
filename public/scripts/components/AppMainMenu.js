

export default {
    data() {
        return {
            mm: [],
            mm_msg: true
        }
    },
    props: {
        ma: Object
    },
    async onBeforeMount() {
        // fetchData() {
            this.$http.post('/ajax/universe/menu/', {reason:'menu'}).then(response => {

                // get body data
                this.mm = response.body;
            }, response => {
                // error callback
                this.mm_msg = 'Error [500]'
            });
        // }
    },
    template: `
    <nav id="menu-august-nav" class="menu-august-nav">
        <div class="menu-august-cover ft-sect">
            <div class="menu-august" give-trans-bck>
                <div class="menu-august-profile">
                    <a href="/{{ mm.username }}/">
                        <div prof-img>
                            <img src="{{ mm.display }}" alt="{{ mm.name }}'s display picture" />
                        </div>
                        <div prof-text>
                            <h1>{{ mm.name }}</h1>
                            <p>@{{ mm.username }}</p>
                        </div>
                    </a>
                </div>
            </div>
            <div class="menu-august" give-botm-space>
                <div class="menu-august-profile">
                    <div class="menu-august-pages">
                        <ul give-und>
                            <a href="/">
                                <li>Home</li>
                            </a>
                            <a href="/{{ mm.username }}/">
                                <li>Profile</li>
                            </a>
                            <a href="/{{ mm.username }}/saved/">
                                <li>Saved</li>
                            </a>
                            <a href="/{{ mm.username }}/history/">
                                <li>History</li>
                            </a>
                            <a href="/{{ mm.username }}/change/">
                                <li>Settings</li>
                            </a>
                        </ul>
                        <ul give-un>
                            <a href="/support/">
                                <li>Help & FAQ</li>
                            </a>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="menu-august">
                <div class="menu-august-profile">
                    <div class="menu-august-profile-mixt">
                        <label class="note-color-mode">
                            <input type="checkbox" class="hd" name="color_mode" mode="{{ mm.theme_state }}" {{ mm.theme_check }} />
                            <div>
                                <h1><i class="{{ mm.theme_icon }}"></i></h1>
                                <p>{{ mm.theme_text }}</p>
                            </div>
                        </label>
                        <a href="/{{ mm.username }}/signout/">
                            <div>
                                <h1><i class="fa-solid fa-right-from-bracket"></i></h1>
                                <p>Log out</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>`,
}